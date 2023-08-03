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
        $parent = $user->parent;

        if ($parent->role == 'sub') {
            $parentBusCredit = $parent->bus_credit;
            if ($parentBusCredit < $data['ticket_price']) {
                return response()->json('error');
            }

            $userBusCredit = $user->bus_credit;
            if ($userBusCredit < $data['ticket_price']) {
                return response()->json('error');
            }
        } else {
            $userBusCredit = $user->bus_credit;
            if ($userBusCredit < $data['ticket_price']) {
                return response()->json('error');
            }
        }

        $this->flixbus->updateReservationItems(
            $data['trip_uid'],
            $data['adult'],
            $data['children'] ?? 0,
            'EUR',
            $request->passengers,
            $request->email
        );

        $result = $this->flixbus->responseData;

        $totalPassengers = $data['adult'] + $data['children'];
        $totalPrice = $data['ticket_price'] * $totalPassengers;

        $busData = [
            'reseller_id' => $user->id,
            'departure_date' => $data['departure_date'],
            'total_passenger' => $totalPassengers,
            'total_price' => $totalPrice,
            'user_email' => $request->email,
            'meta_data' => json_encode($result),
        ];
        $trip = bus::create($busData);

        $busCreditProfit = ($user->bus_credit_profit / 100) * $totalPrice;
        $busCredit = $user->bus_credit - $totalPrice + $busCreditProfit;

        if ($parent->role == 'sub') {
            $parentbusCreditProfit = ($parent->bus_credit_profit / 100) * $totalPrice;
            $userDiscount = $parentbusCreditProfit * ($user->bus_credit_profit / 100);
            $busCredit = $user->bus_credit - $totalPrice + $userDiscount;

            $transaction_id = date('dmyHis') . str_pad($user->id, 4, "0", STR_PAD_LEFT) . str_pad(8, 2, "0", STR_PAD_LEFT);
            $log_data = 'TXID = ' . $transaction_id . ' Amount = ' . ($totalPrice - $userDiscount) . ' Tx-Type = ' . 'Debit' . ' WBT = ' . $user->bus_credit . ' WAT = ' . $busCredit . ' Wallet Type = ' . 'Wallet' . ' Type = ' . 'Bus';
            Log::channel('transactionlog')->info($log_data);
            TransactionHistory::create([
                'reseller_id' => $user->id,
                'transaction_id' => $transaction_id,
                'transaction_source_id' => $trip->id,
                'transaction_type' => 'Debit',
                'transaction_source' => 'Bus',
                'amount' => ($totalPrice - $userDiscount),
                'transaction_wallet' => 'Wallet',
                'wallet_before_transaction' => $user->bus_credit,
                'wallet_after_transaction' => $busCredit,
                'wallet_type' => 'Wallet',
                'parent_id' => $parent->id,
            ]);

            $transaction_id = date('dmyHis') . str_pad($parent->id, 4, "0", STR_PAD_LEFT) . str_pad(8, 2, "0", STR_PAD_LEFT);
            $log_data = 'TXID = ' . $transaction_id . ' Amount = ' . ($totalPrice - $parentbusCreditProfit + $userDiscount) . ' Tx-Type = ' . 'Debit' . ' WBT = ' . $parent->bus_credit . ' WAT = ' . ($parent->bus_credit - $totalPrice + ($parentbusCreditProfit - $userDiscount)) . ' Wallet Type = ' . 'Wallet' . ' Type = ' . 'Bus';
            Log::channel('transactionlog')->info($log_data);
            TransactionHistory::create([
                'reseller_id' => $user->id,
                'transaction_id' => $transaction_id,
                'transaction_source_id' => $trip->id,
                'transaction_type' => 'Debit',
                'transaction_source' => 'Bus',
                'amount' => ($totalPrice - $parentbusCreditProfit + $userDiscount),
                'transaction_wallet' => 'Wallet',
                'wallet_before_transaction' => $parent->bus_credit,
                'wallet_after_transaction' => ($parent->bus_credit - $totalPrice + ($parentbusCreditProfit - $userDiscount)),
                'wallet_type' => 'Wallet',
                'parent_id' => $parent->id,
            ]);

            $parent->update([
                'bus_credit' => $parent->bus_credit - $totalPrice + ($parentbusCreditProfit - $userDiscount),
            ]);

        } else {
            $userDiscount = $busCreditProfit * ($user->bus_credit_profit / 100);
            $busCredit = $user->bus_credit - $totalPrice + $userDiscount;

            $transaction_id = date('dmyHis') . str_pad($user->id, 4, "0", STR_PAD_LEFT) . str_pad(8, 2, "0", STR_PAD_LEFT);
            $log_data = 'TXID = ' . $transaction_id . ' Amount = ' . ($totalPrice - $userDiscount) . ' Tx-Type = ' . 'Debit' . ' WBT = ' . $user->bus_credit . ' WAT = ' . $busCredit . ' Wallet Type = ' . 'Wallet' . ' Type = ' . 'Bus';
            Log::channel('transactionlog')->info($log_data);
            TransactionHistory::create([
                'reseller_id' => $user->id,
                'transaction_id' => $transaction_id,
                'transaction_source_id' => $trip->id,
                'transaction_type' => 'Debit',
                'transaction_source' => 'Bus',
                'amount' => ($totalPrice - $userDiscount),
                'transaction_wallet' => 'Wallet',
                'wallet_before_transaction' => $user->bus_credit,
                'wallet_after_transaction' => $busCredit,
                'wallet_type' => 'Wallet',
                'parent_id' => $parent->id,
            ]);
        }

        $user->update([
            'bus_credit' => $busCredit,
        ]);

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
