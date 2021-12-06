<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DueControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Events\DueRequest;

class WalletController extends Controller
{
    //
    public function index()
    {

        return view('front.wallet-request');
    }
    public function get_wallet_data()
    {
        if(Auth::user()->role != 'admin')
        {
        DueControl::where('reseller_id',Auth::user()->id)->update(['reseller_notification'=>1]);
        $data = DueControl::where('reseller_id',Auth::user()->id)->orderBy('created_at','DESC')->get();
        }
        else{
        DueControl::where('admin_notification',0)->update(['admin_notification'=>1]);
        $data = DueControl::orderBy('created_at','DESC')->get();
        }
        foreach($data as $item)
        {
            $item->requested_date = Carbon::parse($item->created_at)->format('d-m-Y');
            $item->reseller_name = $item->reseller->first_name." ".$item->reseller->last_name;
            if($item->status == 'pending')
            $item->approved_date = 'Pending';
            else
            $item->approved_date = Carbon::parse($item->updated_at)->format('d-m-Y');
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
    public function approved_amount( Request $request)
    {
        $id = $request->id;
        $approved_amount = $request->approved_amount;
        DueControl::where('id',$id)->update(['approved_amount'=>$approved_amount,'status'=>'approved','admin_notification'=>1,'reseller_notification'=>0]);
        event(new DueRequest());
    }
    public function wallet_notification_count()
    {
        if(Auth::user()->role == 'admin')
        {
            $data = DueControl::where('admin_notification',0)->get()->count();
        }
        else
        {
            $data = DueControl::where('reseller_notification',0)->get()->count();
        }

        return $data;
    }
}
