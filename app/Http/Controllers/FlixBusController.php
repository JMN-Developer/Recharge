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

    public function addPassanger(Request $request)
    {

        try {
            $response = $this->flixbus->addPassanger();

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'There was an error while trying to add passanger',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function submitTicket(Request $request)
    {
        $data = $request->validate([
            'trip_uid' => 'required|string',
            'adult' => 'required|integer',
            'children' => 'nullable|integer',
            'departure_date' => 'required',
            'ticket_price' => 'required|numeric',
        ]);

        $user = auth()->user();
        $userBusCredit = $user->bus_credit;

        if ($user->parent && $user->parent->role === 'sub') {
            $parent = $user->parent;
            $parentBusCredit = $parent->bus_credit;
            if ($parentBusCredit < $data['ticket_price']) {
                return response()->json('error: Insufficient parent bus credit');
            }
            if ($userBusCredit < $data['ticket_price']) {
                return response()->json('error: Insufficient user bus credit');
            }
        } else {
            if ($userBusCredit < $data['ticket_price']) {
                return response()->json('error: Insufficient user bus credit');
            }
        }

        // $this->flixbus->updateReservationItems(
        //     $data['trip_uid'],
        //     $data['adult'],
        //     $data['children'] ?? 0,
        //     'EUR',
        //     $request->passengers,
        //     $request->email
        // );
        // $result = $this->flixbus->responseData;

        $result = array(
            'code' => 200,
            'message' => 'OK',
            'order_uid' => '9000000157-jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei',
            'order' => array(
                'id' => '9000000157',
                'download_hash' => 'jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei',
                'trips' => array(
                    0 => array(
                        'ride_uuid' => 'd1a88ff5-de4d-4bee-b3df-9eda0623d6ef',
                        'departure_station' => array(
                            'id' => 1,
                            'uuid' => 'dcbaaee6-9603-11e6-9066-549f350fcb0c',
                            'name' => 'Berlin central bus station',
                            'address' => 'Masurenallee 4-6',
                            'full_address' => 'Masurenallee 4-6, 14057 Berlin, Germany',
                            'coordinates' => array(
                                'latitude' => 52.507171,
                                'longitude' => 13.279399,
                            ),
                            'country' => array(
                                'name' => 'Germany',
                                'alpha2_code' => 'DE',
                            ),
                            'warnings' => 'Gate information available here: https://zob.berlin/en/guests-visitors#departures',
                        ),
                        'arrival_station' => array(
                            'id' => 10,
                            'uuid' => 'dcbabbfa-9603-11e6-9066-549f350fcb0c',
                            'name' => 'Munich central bus station',
                            'address' => 'Arnulfstraße 21',
                            'full_address' => 'Arnulfstraße 21, 80335 München, Germany',
                            'coordinates' => array(
                                'latitude' => 48.14248,
                                'longitude' => 11.55001,
                            ),
                            'country' => array(
                                'name' => 'Germany',
                                'alpha2_code' => 'DE',
                            ),
                            'warnings' => 'Gate information available here: https://www.muenchen-zob.de/en/connections',
                        ),
                        'departure' => array(
                            'timestamp' => 1691214000,
                            'tz' => 'GMT+02:00',
                        ),
                        'arrival' => array(
                            'timestamp' => 1691245500,
                            'tz' => 'GMT+02:00',
                        ),
                        'passengers' => array(
                            0 => array(
                                'type' => 'adult',
                                'uuid' => 'cfc22dbf-a2f5-4ca2-8afe-af03fb46eba2',
                                'firstname' => 'tt',
                                'lastname' => 'tt',
                                'phone' => '',
                            ),
                        ),
                        'transfers' => array(
                        ),
                        'line_direction' => 'Route 141 direction St. Gallen',
                        'line' => array(
                            'number' => '141',
                            'direction' => 'St. Gallen',
                            'brand' => array(
                                'id' => 'a18f138c-68fa-4b45-a42f-adb0378e10d3',
                                'name' => 'FlixBus',
                                'color' => '73d700',
                                'color_light' => 'e5f9c0',
                                'color_dark' => '187d00',
                                'type' => 'core',
                            ),
                        ),
                        'bike_slots_count' => 0,
                        'passbook_url' => 'https://api.stg1.staging.greensystems-dev.flixtech.io/passbook/get/jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei/9000000157-direct:206679253:1:10.pkpass',
                        'order_status' => 'paid',
                        'push_channel_uid' => '/passengers/4386025413',
                        'warnings' => array(
                        ),
                        'trip_uid' => 'direct:206679253:1:10',
                        'products_description' => 'tt tt (Adult)',
                        'products_description_html' => '<b>tt tt</b> <i>(Adult)</i>',
                        'real_time_info_available' => true,
                        'seats_per_relation' => array(
                            0 => array(
                                'from' => 1,
                                'to' => 10,
                                'seats' => array(
                                    0 => array(
                                        'label' => '20D',
                                        'deck' => '0',
                                        'vehicle' => 'bus',
                                        'category' => 'reserved_seat_mobile',
                                        'is_auto_assigned' => false,
                                    ),
                                ),
                            ),
                        ),
                        'operated_by' => array(
                            0 => array(
                                'key' => '',
                                'label' => 'FlixBus DACH GmbH',
                                'url' => '',
                                'address' => 'Karl-Liebknecht-Straße 33',
                            ),
                        ),
                        'self_checkin_available' => true,
                        'transfer_type' => 'direct',
                    ),
                ),
                'invoices' => array(
                    0 => array(
                        'ticket_id' => 'mfb-9000000143',
                    ),
                    1 => array(
                        'ticket_id' => 'mfb-9000000144',
                    ),
                ),
                'info_blocks' => array(
                    0 => array(
                        'name' => 'time',
                        'html' => 'Please be at the stop 15 minutes prior to departure.<br />Your ticket expires at departure time and will be available for purchase again.',
                        'title' => '',
                    ),
                    1 => array(
                        'name' => 'suitcase',
                        'html' => 'pdf-luggage-info',
                        'title' => '',
                    ),
                    2 => array(
                        'name' => 'rebooking',
                        'html' => 'To change your reservation, please visit <a href="https://shop-en.stg1.staging.greensystems-dev.flixtech.io/rebooking/mobile/auth?orderId=9000000157&downloadHash=jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei">https://shop-en.stg1.staging.greensystems-dev.flixtech.io/rebooking</a>',
                        'title' => '',
                    ),
                    3 => array(
                        'name' => 'help',
                        'html' => 'FAQ: <a href="https://help.flixbus.com">https://help.flixbus.com</a><br/><em>* (Please note, that fees for calls from landlines as well as from mobile phones depend on individual provider‘s rates.)</em>',
                        'title' => '',
                    ),
                    4 => array(
                        'name' => 'cabotage-note',
                        'html' => 'This ticket allows the passenger to travel between the start location and destination indicated on the ticket. Boarding later or exiting early is not permitted due to legal requirements.',
                        'title' => '',
                    ),
                    5 => array(
                        'name' => 'invoice',
                        'html' => 'To download your invoice, please visit <a href="https://shop-en.stg1.staging.greensystems-dev.flixtech.io/rebooking">%rebooking_text_url%</a>.',
                        'title' => '',
                    ),
                ),
                'order_uid' => '9000000157-jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei',
                'reminder_link' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei/Ticket-Berlin-Muenchen-9000000157.pdf',
                'invoice_link' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei/Invoice-9000000157.pdf',
                'qr_data' => 'https://shop-de.stg1.staging.greensystems-dev.flixtech.io/pdfqr/9000000157/jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei',
                'qr_image' => 'https://api.stg1.staging.greensystems-dev.flixtech.io/qrcode/en/9000000157/jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei',
                'attachments' => array(
                    0 => array(
                        'title' => 'Passenger rights',
                        'rel' => 'booking:passengers-rights',
                        'href' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/passenger-rights/en_fb_rights.pdf',
                        'type' => 'application/pdf',
                    ),
                    1 => array(
                        'title' => 'QR Code URL',
                        'rel' => 'booking:qr:image',
                        'href' => 'https://api.stg1.staging.greensystems-dev.flixtech.io/qrcode/en/9000000157/jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei',
                        'type' => 'image/png',
                    ),
                    2 => array(
                        'title' => 'QR Code Data',
                        'rel' => 'booking:qr:data',
                        'href' => 'https://shop-de.stg1.staging.greensystems-dev.flixtech.io/pdfqr/9000000157/jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei',
                        'type' => 'text/plain',
                    ),
                    3 => array(
                        'title' => 'Invoice',
                        'rel' => 'booking:invoice',
                        'href' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei/Invoice-9000000157.pdf',
                        'type' => 'application/pdf',
                    ),
                    4 => array(
                        'title' => 'Booking Confirmation',
                        'rel' => 'booking:confirmation',
                        'href' => 'https://mfb-fb-pdf-staging-stg1-323878168362.s3.eu-west-1.amazonaws.com/jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei/Ticket-Berlin-Muenchen-9000000157.pdf',
                        'type' => 'application/pdf',
                    ),
                    5 => array(
                        'title' => 'Rebooking',
                        'rel' => 'shop:rebooking',
                        'href' => 'https://shop-en.stg1.staging.greensystems-dev.flixtech.io/rebooking/mobile/auth?orderId=9000000157&downloadHash=jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei',
                        'type' => 'text/plain',
                    ),
                ),
            ),
            '_links' => array(
                'self' => array(
                    'href' => 'http://staging-stg1-mfb-api-web-webc-green-dev.origin-api-gw.ew1d2.k8s-dev.flix.tech/mobile/v2/orders/9000000157/info?download_hash=jhj1ocsuv4bgv1tj0fgl2ixxrlbf180iujlnalpu7hh96xkhei',
                ),
                'cancel' => array(
                    'href' => 'http://staging-stg1-mfb-api-web-webc-green-dev.origin-api-gw.ew1d2.k8s-dev.flix.tech/public/v2/orders/9000000157/cancel',
                ),
            ),
        );

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

            Log::info($totalPrice);
            Log::info($userDiscount);
            Log::info($parentDiscount);
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

            ];
        }

        return view('front.flixbus.bus-ticket-list', ['busTickets' => $data]);
    }

    private function isTicketCancelable($purchaseDateTime)
    {

        $cancelationWindow = 15 * 60;
        $currentTime = time();
        $purchaseTime = strtotime($purchaseDateTime);
        $timeDifference = $currentTime - $purchaseTime;

        // If the time difference is within the cancelation window, the ticket can be canceled.
        return $timeDifference <= $cancelationWindow;
    }

    public function cancelTicket(Request $request)
    {
        $id = $request->id;
        $reseller_id = auth()->user()->id;
        Log::info($id);
        $busTicket = Bus::where('id', $id)->first();
        $metaData = json_decode($busTicket->meta_data, true);
        $order_hash = $metaData['order']['download_hash'];
        $order_id = $metaData['order']['id'];
        $this->flixbus->cancelOrder($order_id, $order_hash);
        $cancelData = $this->cancelInvoice($order_id, $order_hash);
    }

    public function cancelInvoice($order_id, $order_hash)
    {
        $order_hash = 'liq4se8d1gj3krr1noa898h4tftgw1sp7avfukj5p4jvj5v2dd';
        $order_id = '9000000335';
        return $this->flixbus->cancelInvoice($order_id, $order_hash);
    }

}
