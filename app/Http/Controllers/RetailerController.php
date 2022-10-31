<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ResellerProfit;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class RetailerController extends Controller
{
    public function retailer_details()
    {
        if (Auth::user()->role == 'admin') {
            $data = User::orderBy('limit_usage', 'DESC')->get();
        } else {
            $data = User::where('role', 'sub')->where('created_by', Auth::user()->id)->orderBy('limit_usage', 'DESC')->get();
        }
        // dd($data);
        return view('front.retailer_details', compact('data'));
    }

    public function RetailerDetail($value = '')
    {
        if (Auth::user()->role == 'admin') {
            $data = User::where('role', 'user')->get();
        } else {
            $data = User::where('role', 'user')->where('created_by', Auth::user()->id)->get();
        }
        // dd($data);
        return view('front.retailer-details', compact('data'));
    }

    public function RetailerAction($value = '')
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'admin2') {
            $data = User::where('role', '!=', 'admin')->get();
        } else {
            $data = User::where('created_by', Auth::user()->id)->get();
        }
        // file_put_contents('test.txt',auth()->user()->role);
        return view('front.retailer-action', compact('data'));
    }
    public function RetailerSignUp($value = '')
    {
        return view('front.registration-form');
    }
    public function changeStatus(Request $request)
    {

        $user = User::find($request->user_id);
        $user->recharge_permission = $request->status;
        $user->save();

        return response()->json(['message' => 'success']);

    }

    public function changeSim(Request $request)
    {
        $user = User::find($request->user_id);
        $user->sim_permission = $request->status;
        $user->save();

        return response()->json(['success' => 'Status change successfully.']);
    }
    public function changeCargo(Request $request)
    {
        $user = User::find($request->user_id);
        $user->cargo_permission = $request->status;
        $user->save();

        return response()->json(['success' => 'Status change successfully.']);
    }
    public function changePhone(Request $request)
    {
        $user = User::find($request->user_id);
        $user->mobile_permission = $request->status;
        $user->save();

        return response()->json(['success' => 'Status change successfully.']);
    }

    public function changeReseller(Request $request)
    {
        $user = User::find($request->user_id);
        $user->reseller_permission = $request->status;
        $user->save();

        return response()->json(['success' => 'Status change successfully.']);
    }
    public function changePin(Request $request)
    {
        $user = User::find($request->user_id);
        $user->pin_permission = $request->status;
        $user->save();

        return response()->json(['success' => 'Status change successfully.']);
    }
    public function AddCom(Request $request)
    {
        if (Auth::user()->role == 'admin') {
            $user = User::where('id', $request->user_id)->update([

                'cargo_goods_profit' => $request->cargo_goods_profit,
                'cargo_documents_profit' => $request->cargo_documents_profit,
                'admin_recharge_commission' => $request->domestic_recharge_profit,
                'admin_international_recharge_commission' => $request->international_recharge,
                'admin_pin_commission' => $request->pin,
            ]);
            $user = ResellerProfit::where('reseller_id', $request->user_id)->first();
            // if($user)
            // {
            // ResellerProfit::where('reseller_id',$request->user_id)->update([
            //     'reseller_id'=>$request->user_id,
            //     'international_recharge_profit'=>$request->international_recharge_profit,
            //     'domestic_recharge_profit'=>$request->domestic_recharge_profit,
            // ]);
            // }
            // else
            // {
            //     ResellerProfit::create([
            //         'reseller_id'=>$request->user_id,
            //         'international_recharge_profit'=>$request->international_recharge_profit,
            //         'domestic_recharge_profit'=>$request->domestic_recharge_profit,
            //     ]);
            // }

        } else {
            $user = User::where('id', $request->user_id)->update([
                'mobile' => $request->mobile,
                'sim' => $request->sim,
                'cargo' => $request->cargo,
                'recharge' => $request->recharge,
                'international_recharge' => $request->international_recharge,
                'pin' => $request->pin,
            ]);

        }

        return back()->with('status', 'Commission Set Suucessfully!');

    }
}
