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
            $parentBusLimit = $parent->bus_credit_limit;
            if ($parentBusCredit + $parentBusLimit < $data['ticket_price']) {
                return response()->json('Insufficient wallet');
            }
            $user = User::find(auth()->user()->id);
            $userBusCredit = $user->bus_credit;
            $userBusLimit = $user->bus_credit_limit;
            if ($userBusCredit + $userBusLimit < $data['ticket_price']) {
                return response()->json('Insufficient wallet');
            }
        } else {
            $user = User::find(auth()->user()->id);
            $userBusCredit = $user->bus_credit;
            $userBusLimit = $user->bus_credit_limit;
            if ($userBusCredit + $userBusLimit < $data['ticket_price']) {
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
            'service_charge' => $request->serviceCharge,
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
                'bus_credit' => ($parent->bus_credit - $totalPrice + $parentDiscount),
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

        $buses = Bus::select('id', 'user_email', 'departure_date', 'total_price', 'created_at', 'cancel_status', 'service_charge', 'meta_data', 'cancel_meta_data')
                    ->where('reseller_id', $reseller_id)
                    ->orderBy('created_at', 'DESC')
                    ->get();

        $tickets = $buses->map(function ($bus) {
            return $this->transformBusToTicketData($bus);
        });

        return view('front.flixbus.bus-ticket-list', ['busTickets' => $tickets]);
    }

    private function transformBusToTicketData($bus)
    {
        $metaData = json_decode($bus->meta_data, true);

        // Ensure the necessary data exists to avoid errors
        $trip = $metaData['order']['trips'][0] ?? null;
        $attachments = $metaData['order']['attachments'] ?? [];

        $departureStationName = $trip['departure_station']['name'] ?? '';
        $arrivalStationName = $trip['arrival_station']['name'] ?? '';
        $totalPassengers = sizeof($trip['passengers'] ?? []);

        return [
            'id' => $bus->id,
            'user_email' => $bus->user_email,
            'departure_station_name' => $departureStationName,
            'arrival_station_name' => $arrivalStationName,
            'departure_date' => $bus->departure_date,
            'total_passengers' => $totalPassengers,
            'ticket_unit_price' => $totalPassengers ? $bus->total_price / $totalPassengers : 0,
            'ticket_total_price' => $bus->total_price,
            'ticket_purchase_date' => $bus->created_at,
            'status' => $bus->cancel_status,
            'service_charge' => $bus->service_charge,
            'passenger_rights' => $attachments[0]['href'] ?? '',
            'qr_code_url' => $attachments[1]['href'] ?? '',
            'qr_code_data' => $attachments[2]['href'] ?? '',
            'invoice' => $attachments[3]['href'] ?? '',
            'booking_confirmation' => $attachments[4]['href'] ?? '',
            'cancel_available_status' => $this->isTicketCancelable($bus->created_at),
            'cancel_meta' => $bus->cancel_status == 1 ? json_decode($bus->cancel_meta_data, true) : null,
        ];
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
        $this->flixbus->cancelOrder($order_id, $order_hash);
        $cancelData = $this->cancelInvoice($order_id, $order_hash);

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
