<?php

namespace App\Http\Controllers;

use App\Models\bus;
use App\Models\BusCity;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Services\Flixbus;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

class FlixBusController extends Controller
{
    private $flixbus;

    public function __construct(Flixbus $flixbus)
    {
        $this->flixbus = $flixbus;
    }
    //
    public function index()
    {
        $cities = BusCity::get();
        return view('front.flixbus.index', compact('cities'));
    }

    public function searchTrip(Request $request)
    {
        $search_by = 'cities';
        $from = $request->from;
        $to = $request->destination;
        $departure_date = Carbon::createFromFormat('d.m.Y', $request->departureDate)->format('d.m.Y');
        $adult = $request->adultCount;
        $children = $request->childCount;
        $bikes = 0;
        $response = $this->flixbus->searchTrips($search_by, $from, $to, $departure_date, $adult, $children, $bikes);
        return response()->json($response);
    }

    public function getCities()
    {
        $response = $this->flixbus->getCities();
        return response()->json($response);
    }

    public function updateReservationItems(Request $request)
    {
        $data = $request->validate([
            'trip_uid' => 'required|string',
            'adult' => 'required|integer',
            'currency' => 'required|string',

        ]);

        $result = $this->flixbus->updateReservationItems(
            $data['trip_uid'],
            $data['adult'],
            $data['children'] ?? 0,
            $data['currency'],
        );

        return response()->json($result);
    }
    public function initiatePayment(Request $request)
    {
        $validatedData = $request->validate([
            'reservation' => 'required',
            'reservation_token' => 'required',
            'email' => 'required|email',
            'payment.psp' => 'required',
            'payment.method' => 'required',
        ]);

        try {
            $response = $this->flixbus->startPayment(
                $validatedData['reservation'],
                $validatedData['reservation_token'],
                $validatedData['email'],
                $validatedData['payment']['psp'],
                $validatedData['payment']['method']
            );

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'There was an error while trying to start the payment',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function submitTicket(Request $request)
    {

        $modifiedPassengers = [];
        if ($request->has('passengers')) {
            foreach ($request->passengers as $passenger) {
                if (isset($passenger['birthdate'])) {

                    $passenger['birthdate'] = date("d.m.Y", strtotime($passenger['birthdate']));

                }

                $modifiedPassengers[] = $passenger;

            }
        }

        $data = $request->validate([
            'trip_uid' => 'required|string',
            'adult' => 'required|integer',
            'children' => 'nullable',
            'departure_date' => 'required',
            'ticket_price' => 'required',

        ]);
        if (auth()->user()->parent->role == 'sub') {
            $parent = User::find(auth()->user()->parent->id);
            $parentBusCredit = $parent->bus_credit;
            if ($parentBusCredit < $data['ticket_price']) {
                return response()->json('Insufficient wallet');
            }
            $user = User::find(auth()->user()->id);
            $userBusCredit = $user->bus_credit;
            if ($userBusCredit < $data['ticket_price']) {
                return response()->json('Insufficient wallet');
            }
        } else {
            $user = User::find(auth()->user()->id);
            $userBusCredit = $user->bus_credit;
            if ($userBusCredit < $data['ticket_price']) {
                return response()->json('Insufficient wallet');
            }
        }

        $this->flixbus->updateReservationItems(
            $data['trip_uid'],
            $data['adult'],
            $data['children'] ?? 0,
            'EUR',
            $modifiedPassengers,
            $request->email
        );

        $result = $this->flixbus->responseData;

        $totalPrice = $data['ticket_price'] * ($data['adult'] + $data['children']);
        $trip = bus::create([
            'reseller_id' => $user->id,
            'departure_date' => $data['departure_date'],
            'total_passenger' => $data['adult'] + $data['children'],
            'total_price' => $totalPrice,
            'user_email' => $request->email,
            'meta_data' => json_encode($result),
        ]);
        if ($user->parent && $user->parent->role === 'sub') {
            $parent = $user->parent;
            $parentbusCreditProfit = $parent->bus_credit_profit / 100;
            $parentDiscount = $totalPrice * 0.07 * $parentbusCreditProfit;
            $userBusCreditProfit = $user->bus_credit_profit / 100;
            $userDiscount = $parentDiscount * $userBusCreditProfit;
            $busCredit = $userBusCredit - $totalPrice + $userDiscount;

            $transaction_id = date('dmyHis') . str_pad($user->id, 4, "0", STR_PAD_LEFT) . str_pad(8, 2, "0", STR_PAD_LEFT);
            $log_data = 'TXID = ' . $transaction_id . ' Amount = ' . ($totalPrice - $userDiscount) . ' Tx-Type = Debit WBT = ' . $userBusCredit . ' WAT = ' . $busCredit . ' Wallet Type = Wallet Type = Bus';
            Log::channel('transactionlog')->info($log_data);
            TransactionHistory::create([
                'reseller_id' => $user->id,
                'transaction_id' => $transaction_id,
                'transaction_source_id' => $trip->id,
                'transaction_type' => 'Debit',
                'transaction_source' => 'Bus',
                'amount' => ($totalPrice - $userDiscount),
                'transaction_wallet' => 'Bus',
                'wallet_before_transaction' => $userBusCredit,
                'wallet_after_transaction' => $busCredit,
                'wallet_type' => 'Wallet',
                'parent_id' => $user->parent->id,
            ]);

            $user->update([
                'bus_credit' => $busCredit,
            ]);

            $transaction_id = date('dmyHis') . str_pad($user->parent->id, 4, "0", STR_PAD_LEFT) . str_pad(8, 2, "0", STR_PAD_LEFT);
            $log_data = 'TXID = ' . $transaction_id . ' Amount = ' . ($totalPrice - $parentDiscount + $userDiscount) . ' Tx-Type = Debit WBT = ' . $parent->bus_credit . ' WAT = ' . ($parent->bus_credit - $totalPrice + ($parentDiscount - $userDiscount)) . ' Wallet Type = Wallet Type = Bus';
            Log::channel('transactionlog')->info($log_data);
            TransactionHistory::create([
                'reseller_id' => $parent->id,
                'transaction_id' => $transaction_id,
                'transaction_source_id' => $trip->id,
                'transaction_type' => 'Debit',
                'transaction_source' => 'Bus',
                'amount' => ($totalPrice - $parentDiscount + $userDiscount),
                'transaction_wallet' => 'Bus',
                'wallet_before_transaction' => $parent->bus_credit,
                'wallet_after_transaction' => ($parent->bus_credit - $totalPrice + ($parentDiscount - $userDiscount)),
                'wallet_type' => 'Wallet',
                'parent_id' => $parent->parent->id,
            ]);
            $parent->update([
                'bus_credit' => ($parent->bus_credit - $totalPrice + ($parentDiscount - $userDiscount)),
            ]);
        } else {
            $busCreditProfit = $user->bus_credit_profit / 100;
            $userDiscount = $totalPrice * 0.07 * $busCreditProfit;
            $busCredit = $userBusCredit - $totalPrice + $userDiscount;

            $transaction_id = date('dmyHis') . str_pad($user->id, 4, "0", STR_PAD_LEFT) . str_pad(8, 2, "0", STR_PAD_LEFT);
            $log_data = 'TXID = ' . $transaction_id . ' Amount = ' . ($totalPrice - $userDiscount) . ' Tx-Type = Debit WBT = ' . $userBusCredit . ' WAT = ' . $busCredit . ' Wallet Type = Wallet Type = Bus';
            Log::channel('transactionlog')->info($log_data);
            TransactionHistory::create([
                'reseller_id' => $user->id,
                'transaction_id' => $transaction_id,
                'transaction_source_id' => $trip->id,
                'transaction_type' => 'Debit',
                'transaction_source' => 'Bus',
                'amount' => ($totalPrice - $userDiscount),
                'transaction_wallet' => 'Bus',
                'wallet_before_transaction' => $userBusCredit,
                'wallet_after_transaction' => $busCredit,
                'wallet_type' => 'Wallet',
                'parent_id' => $user->parent->id,
            ]);

            $user->update([
                'bus_credit' => $busCredit,
            ]);
        }

        return response()->json($result);
    }
    public function busTicketList()
    {
        $reseller_id = auth()->user()->id;
        $busTickets = Bus::where('reseller_id', $reseller_id)->orderBY('created_at', 'DESC')->get();

        $data = [];
        foreach ($busTickets as $busTicket) {
            $metaData = json_decode($busTicket->meta_data, true);
            $departureStationName = $metaData['order']['trips'][0]['departure_station']['name'];
            $arrivalStationName = $metaData['order']['trips'][0]['arrival_station']['name'];
            $totalPassengers = sizeof($metaData['order']['trips'][0]['passengers']);
            $cancelAvailableStatus = $this->isTicketCancelable($busTicket->created_at);
            $data[] = [
                'id' => $busTicket->id,
                'user_email' => $busTicket->user_email,
                'departure_station_name' => $departureStationName,
                'arrival_station_name' => $arrivalStationName,
                'departure_date' => $busTicket->departure_date,
                'total_passengers' => $totalPassengers,
                'ticket_unit_price' => $busTicket->total_price / $totalPassengers,
                'ticket_total_price' => $busTicket->total_price,
                'ticket_purchase_date' => $busTicket->created_at,
                'status' => $busTicket->cancel_status,

                'passenger_rights' => $metaData['order']['attachments'][0]['href'],
                'qr_code_url' => $metaData['order']['attachments'][1]['href'],
                'qr_code_data' => $metaData['order']['attachments'][2]['href'],
                'invoice' => $metaData['order']['attachments'][3]['href'],
                'booking_confirmation' => $metaData['order']['attachments'][4]['href'],
                'cancel_available_status' => $cancelAvailableStatus,
                'cancel_meta' => $busTicket->cancel_status == 1 ? json_decode($busTicket->cancel_meta_data, true) : null,

            ];
        }

        return view('front.flixbus.bus-ticket-list', ['busTickets' => $data]);
    }

    public function getOrderDetails()
    {
        return $this->flixbus->getOrderInfo('9000000019', 'q3dp5ap7p56jiw5wxbzlqwnhaoauyfjuxi0a9t4hfy466z5f3x');
    }
    private function isTicketCancelable($purchaseDateTime)
    {
        return true;

        // $cancelationWindow = 15 * 60;
        // $currentTime = time();
        // $purchaseTime = strtotime($purchaseDateTime);
        // $timeDifference = $currentTime - $purchaseTime;

        // // If the time difference is within the cancelation window, the ticket can be canceled.
        // return $timeDifference <= $cancelationWindow;
    }

    public function cancelTicket(Request $request)
    {
        $id = $request->id;
        $reseller_id = auth()->user()->id;
        $busTicket = Bus::where('id', $id)->first();
        $metaData = json_decode($busTicket->meta_data, true);
        $order_hash = $metaData['order']['download_hash'];
        $order_id = $metaData['order']['id'];
        //$this->flixbus->cancelOrder($order_id, $order_hash);
        //$cancelData = $this->cancelInvoice($order_id, $order_hash);
        $cancelData = array(
            'cancellation_invoice' => array(
                'cancellation_invoice_items' => array(
                    0 => array(
                        'type' => 'adult',
                        'invoice_number' => 'flixital-3035960453',
                        'net_value' => -4.54,
                        'tax_value' => -0.45,
                        'total_gross_value' => -4.99,
                        'taxes' => array(
                            0 => array(
                                'tax_percent' => 10,
                                'tax_value' => -0.45,
                                'net_value' => -4.99,
                                'country' => 'IT',
                            ),
                        ),
                        'donation_value' => -0.06,
                        'ride' => array(
                            'concession_owner' => array(
                                'name' => 'FlixBus Italia S.r.l.',
                                'url' => 'https://www.flixbus.it',
                                'street' => 'Corso Como 11',
                                'zip_code' => '20154',
                                'managers' => array(
                                    0 => 'Andrea Incondi',
                                    1 => ' Max Zeumer ',
                                ),
                                'city_name' => 'Milano',
                                'tax_id' => 'IT 08776680962',
                            ),
                            'from' => 'Trieste (Autostazione)',
                            'to' => 'Rome Tiburtina Bus station',
                            'departure' => array(
                                'datetime' => '2023-08-30T18:40:00Z',
                                'tz_id' => 'Europe/Rome',
                            ),
                            'arrival' => array(
                                'datetime' => '2023-08-30T19:45:00Z',
                                'tz_id' => 'Europe/Rome',
                            ),
                        ),
                        'passenger' => array(
                            'first_name' => 'test',
                            'last_name' => 'test',
                            'type' => 'adult',
                        ),
                        'source_invoice_number' => 'flixital-3035303381',
                    ),
                    1 => array(
                        'type' => 'premium_seat',
                        'invoice_number' => 'flixital-3035960454',
                        'net_value' => 0,
                        'tax_value' => 0,
                        'total_gross_value' => 0,
                        'taxes' => array(
                            0 => array(
                                'tax_percent' => 10,
                                'tax_value' => 0,
                                'net_value' => 0,
                                'country' => 'IT',
                            ),
                        ),
                        'donation_value' => 0,
                        'ride' => array(
                            'concession_owner' => array(
                                'name' => 'FlixBus Italia S.r.l.',
                                'url' => 'https://www.flixbus.it',
                                'street' => 'Corso Como 11',
                                'zip_code' => '20154',
                                'managers' => array(
                                    0 => 'Andrea Incondi',
                                    1 => ' Max Zeumer ',
                                ),
                                'city_name' => 'Milano',
                                'tax_id' => 'IT 08776680962',
                            ),
                            'from' => 'Trieste (Autostazione)',
                            'to' => 'Rome Tiburtina Bus station',
                            'departure' => array(
                                'datetime' => '2023-08-30T18:40:00Z',
                                'tz_id' => 'Europe/Rome',
                            ),
                            'arrival' => array(
                                'datetime' => '2023-08-30T19:45:00Z',
                                'tz_id' => 'Europe/Rome',
                            ),
                        ),
                        'source_invoice_number' => 'flixital-3035303382',
                    ),
                ),
            ),
            'voucher' => array(
                'code' => 'REB7FPUAFGAL',
                'value' => 5.05,
                'expires_at' => '2024-08-21T23:59:59+0200',
                'currency' => array(
                    'code' => 'EUR',
                    'symbol' => '€',
                ),
            ),
            'documents' => array(
                0 => array(
                    'name' => 'Print voucher code',
                    'rel' => 'voucher',
                    'href' => 'https://mfb-fb-pdf-prod.s3.eu-central-1.amazonaws.com/zxx2qq86gla8wwhdsayv833ostzd3zy8ds57duh0xofvdlda8v/Voucher-3099182198.pdf',
                    'mime_type' => 'application/pdf',
                ),
                1 => array(
                    'name' => 'Print cancelation Invoice',
                    'rel' => 'cancellation_invoice',
                    'href' => 'https://finance-fs-invoice-pdf.s3.eu-west-1.amazonaws.com/zxx2qq86gla8wwhdsayv833ostzd3zy8ds57duh0xofvdlda8v/Cancellation-Invoice-3099182198.pdf',
                    'mime_type' => 'application/pdf',
                ),
            ),
        );

        if (array_key_exists('code', $cancelData)) {
            return response()->json(['status' => false]);
        } else {
            Bus::where('id', $id)->update(['cancel_meta_data' => json_encode($cancelData), 'cancel_status' => 1]);
            return response()->json(['status' => true]);
        }
    }

    public function cancelInvoice($order_id, $order_hash)
    {

        return $this->flixbus->cancelInvoice($order_id, $order_hash);
    }
}
