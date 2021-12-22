<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DueControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Events\DueRequest;
use App\Models\User;
use DB;

class WalletController extends Controller
{
    //
    public function index()
    {

        return view('front.wallet-request');
    }
    public function get_wallet_data()
    {
        if(auth()->user()->role != 'admin')
        {
        DueControl::where('reseller_id',Auth::user()->id)->update(['reseller_notification'=>1]);
        $data = DueControl::where('reseller_id',Auth::user()->id)->orderBy(DB::raw('case when status= "pending" then 1 when status= "declined" then 2 when status="approved" then 3 end'))->get();
        }
        else{
        DueControl::where('admin_notification',0)->update(['admin_notification'=>1]);
        $data = DueControl::orderBy(DB::raw('case when status= "pending" then 1 when status= "declined" then 2 when status="approved" then 3 end'))->get();
        }
        foreach($data as $item)
        {
            $item->requested_date = Carbon::parse($item->created_at)->format('Y-m-d');
            $item->reseller_name = $item->reseller->first_name." ".$item->reseller->last_name;
            $item->limit_usage = $item->reseller->limit_usage;
            if($item->status == 'pending')
            $item->approved_date = 'Pending';
            else
            $item->approved_date = Carbon::parse($item->updated_at)->format('Y-m-d');
        }
        return $data;
    }
    public function wallet_request(Request $request)
    {
        $amount = $request->amount;
        DueControl::create([
            'reseller_id'=>Auth::user()->id,
            'requested_amount'=>$amount,
            'message'=>$request->message,
            'reseller_notification'=>1
        ]);
        event(new DueRequest());

    }

    public function get_requested_amount(Request $request)
    {
        $id = $request->id;
        $data = DueControl::where('id',$id)->first();
        return $data;

    }
    public function update_balance($id,$approved_amount)
    {
        $user = User::where('id',$id)->first();
        $cuurent_balance = $user->wallet;
        $new_balance =$cuurent_balance+$approved_amount;
        User::where('id',$id)->update(['wallet'=>$new_balance]);
    }
    public function approved_amount( Request $request)
    {
        $id = $request->id;
        $approved_amount = $request->approved_amount;
        $admin_message = $request->admin_message;
        $status = $request->status;
        $previous_record = DueControl::where('id',$id)->first();
        $limit_usage = User::where('id',$previous_record->reseller_id)->first()->limit_usage;
        $approved_amount = $approved_amount-$limit_usage;


        if($previous_record->status =='declined')
        {
            if($status =='declined')
            {
                DueControl::where('id',$id)->update(['decline_status'=>1,'status'=>$status,'reseller_notification'=>0,'admin_notification'=>1,'admin_message'=>$admin_message]);
            }
            else{
            DueControl::create([
                'reseller_id'=>$previous_record->reseller_id,
                'requested_amount'=> $previous_record->requested_amount,
                'approved_amount'=>$approved_amount,
                'message'=>$admin_message,
                'reseller_notification'=>0,
                'admin_notification'=>1,
                'status'=>$status,
            ]);
            DueControl::where('id',$id)->update(['decline_status'=>1]);
            User::where('id',$previous_record->reseller_id)->update(['limit_usage'=>0]);
        }

        }
        else{
        if($status=='declined')
        {
        DueControl::where('id',$id)->update(['status'=>$status,'admin_notification'=>1,'reseller_notification'=>0,'admin_message'=>$admin_message]);
        }
        else{
        DueControl::where('id',$id)->update(['approved_amount'=>$approved_amount,'status'=>$status,'admin_notification'=>1,'reseller_notification'=>0,'admin_message'=>$admin_message]);
        }
        User::where('id',$previous_record->reseller_id)->update(['limit_usage'=>0]);
    }
        $this->update_balance($previous_record->reseller_id,$approved_amount);


        event(new DueRequest());
    }
    public function wallet_notification_count()
    {
        if(auth()->user()->role == 'admin')
        {
            $data = DueControl::where('admin_notification',0)->get()->count();
        }
        else
        {
            $data = DueControl::where('reseller_id',Auth::user()->id)->where('reseller_notification',0)->get()->count();
        }

        return $data;
    }
}
