<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DueControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Events\DueRequest;
use App\Models\User;
use App\Services\UpdateWallet;
use DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    //
    public function index()
    {
        return view("front.wallet-request-send");
    }
    public function wallet_request_receive()
    {
        return view("front.wallet-request-receive");
    }
    public function get_wallet_data()
    {
        if(auth()->user()->role == 'admin'){
            // DueControl::where("reseller_id", Auth::user()->id)->update([
            //     "reseller_notification" => 1,
            // ]);
            $data = DueControl::where("reseller_type", 'user')
            ->orderBy(
                DB::raw(
                    'case when status= "pending" then 1 when status= "declined" then 2 when status="approved" then 3 end'
                )
            )
            ->get();
        }
        else if(auth()->user()->role =='user'){

            $data = DueControl::where("reseller_type", 'reseller')->where('reseller_parent',Auth::user()->id)
            ->orderBy(
                DB::raw(
                    'case when status= "pending" then 1 when status= "declined" then 2 when status="approved" then 3 end'
                )
            )
            ->get();
        }
        else{
            $data = DueControl::where("reseller_id", Auth::user()->id)
            ->orderBy(
                DB::raw(
                    'case when status= "pending" then 1 when status= "declined" then 2 when status="approved" then 3 end'
                )
            )
            ->get();
        }

        foreach ($data as $item) {
            $item->requested_date = Carbon::parse($item->created_at)->format(
                "Y-m-d"
            );
            $item->reseller_name =
                $item->reseller->first_name . " " . $item->reseller->last_name;
            if($item->wallet_type == 'International')
            $item->limit_usage = $item->reseller->limit_usage;
            else
            $item->limit_usage = $item->reseller->domestic_limit_usage;

            if ($item->status == "pending") {
                $item->approved_date = "Pending";
            } else {
                $item->approved_date = Carbon::parse($item->approved_at)->format(
                    "Y-m-d"
                );
               // $item->approved_date = $item->updated_at;
            }
        }
        return $data;
    }
    public function wallet_request(Request $request)
    {
        $path = $request->document->store('image/paymentSlip', 'public');
        DueControl::create([
            "reseller_id" => Auth::user()->id,
            "requested_amount" => $request->amount,
            "message" => $request->message,
            "wallet_type" => $request->wallet_type,
            "reseller_notification" => 1,
            'document'=>$path,
            "reseller_type" =>Auth::user()->role,
            'reseller_parent' =>Auth::user()->parent->id

        ]);
        try{
            event(new DueRequest());
        }
        catch(\Throwable $th){
            Log::error("Wallet Event Error: ".$th);
        }
       
    }

    public function get_requested_amount(Request $request)
    {
        $id = $request->id;
        $data = DueControl::where("id", $id)->first();
        return $data;
    }
    public function update_limit($id,$amount,$wallet_type)
    {
        if ($wallet_type == "International") {
            User::where("id", $id)->update([
                "limit_usage" => $amount,
            ]);
        } else {
            user::where("id",$id)->update([
                "domestic_limit_usage" => $amount,
            ]);
        }
    }
    public function update_balance($id, $approved_amount,$wallet_type)
    {

        $user = User::where("id", $id)->first();
        if($wallet_type == 'International')
        {
        $cuurent_balance = $user->wallet;
        $new_balance = $cuurent_balance + $approved_amount;
        User::where("id", $id)->update(["wallet" => $new_balance]);
        }
        else
        {
        $cuurent_balance = $user->domestic_wallet;
        $new_balance = $cuurent_balance + $approved_amount;
        User::where("id", $id)->update(["domestic_wallet" => $new_balance]);
        }
    }

    public function create_transaction($id,$transaction_source,$wallet_before_transaction,$wallet_after_transaction,$approved_amount,$wallet_type,$reseller_id)
    {

        UpdateWallet::create_transaction(
            $id,
            "credit",
            $transaction_source,
            $wallet_before_transaction,
            $wallet_after_transaction,
            $approved_amount,
            $wallet_type,
            $reseller_id
        );

    }
    public function approved_amount(Request $request)
    {
        $id = $request->id;
        $approved_amount = $request->approved_amount;
        $admin_message = $request->admin_message;
        $status = $request->status;
        $previous_record = DueControl::where("id", $id)->first();
        $user = User::where("id", $previous_record->reseller_id)->first();

        if ($previous_record->wallet_type == "International") {
            $limit_usage = $user->limit_usage;
            $wallet = $user->wallet;
        } else {
            $limit_usage = $user->domestic_limit_usage;
            $wallet = $user->domestic_wallet;
        }
        if($approved_amount>=$limit_usage)
        {
        $approved_amount = $approved_amount - $limit_usage;
        $this->update_limit($previous_record->reseller_id,0,$previous_record->wallet_type);

        $this->update_balance($previous_record->reseller_id, $approved_amount,$previous_record->wallet_type);
        }


        else
        {
        $approved_amount = $limit_usage - $approved_amount;
        $this->update_limit($previous_record->reseller_id,$approved_amount,$previous_record->wallet_type);
        }

        if ($previous_record->status == "declined") {
            if ($status == "declined") {
                DueControl::where("id", $id)->update([
                    "decline_status" => 1,
                    "status" => $status,
                    "reseller_notification" => 0,
                    "admin_notification" => 1,
                    "admin_message" => $admin_message,

                ]);
            } else {
                DueControl::create([
                    "reseller_id" => $previous_record->reseller_id,
                    "requested_amount" => $previous_record->requested_amount,
                    "approved_amount" => $approved_amount,
                    "message" => $admin_message,
                    "reseller_notification" => 0,
                    "admin_notification" => 1,
                    "status" => $status,
                    "wallet_type" => $previous_record->wallet_type,
                    "previous_due" => $limit_usage,
                    "decline_status" => 1,
                    'approved_at'=>Carbon::now()->toDateTimeString()

                ]);


            }
        } else {
            if ($status == "declined") {
                DueControl::where("id", $id)->update([
                    "status" => $status,
                    "admin_notification" => 1,
                    "reseller_notification" => 0,
                    "admin_message" => $admin_message,
                ]);
            } else {
                DueControl::where("id", $id)->update([
                    "approved_amount" => $approved_amount,
                    "status" => $status,
                    "admin_notification" => 1,
                    "reseller_notification" => 0,
                    "admin_message" => $admin_message,
                    'previous_due'=> $limit_usage,
                    'approved_at'=>Carbon::now()->toDateTimeString()
                ]);

            }


        }
        if ($limit_usage > 0) {

            $wallet_before_transaction = $limit_usage;
            $wallet_after_transaction = 0;
            $this->create_transaction($id,$previous_record->wallet_type,$wallet_before_transaction, $wallet_after_transaction, $limit_usage,'limit',$previous_record->reseller_id);
            $wallet_before_transaction = $wallet;
            $wallet_after_transaction = $approved_amount + $wallet;
            $this->create_transaction($id,$previous_record->wallet_type,$wallet_before_transaction, $wallet_after_transaction, $approved_amount,'wallet',$previous_record->reseller_id);

        } else {
            $wallet_before_transaction = $wallet;
            $wallet_after_transaction = $approved_amount + $wallet;
            $this->create_transaction($id,$previous_record->wallet_type,$wallet_before_transaction, $wallet_after_transaction, $approved_amount,'wallet',$previous_record->reseller_id);

        }
        // if($approved_amount>=$limit_usage){
        // $this->update_limit($previous_record->reseller_id,0,$previous_record->wallet_type);

        // $this->update_balance($previous_record->reseller_id, $approved_amount,$previous_record->wallet_type);
        // }
        // else
        // {
        //     $this->update_limit($previous_record->reseller_id,$approved_amount,$previous_record->wallet_type);
        // }

        //event(new DueRequest());
    }
    public function wallet_notification_count()
    {
        if (auth()->user()->role == "admin") {
            $data = DueControl::where("admin_notification", 0)
                ->get()
                ->count();
        } else {
            $data = DueControl::where("reseller_id", Auth::user()->id)
                ->where("reseller_notification", 0)
                ->get()
                ->count();
        }

        return $data;
    }
}
