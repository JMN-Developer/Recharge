<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RechargeHistory;
use Auth as a;

class ReportController extends Controller
{
    //
    public function index()
    {
        if(a::user()->role == 'admin'){
            $data = RechargeHistory::latest()->get();
            $cost = $data->sum('amount');
            $profit = $data->sum('admin_com');
        }else{
            $data = RechargeHistory::where('reseller_id', a::user()->id)->latest()->get();
            $cost = $data->sum('cost');
            $profit = $data->sum('reseller_com');
        }

        $resellers = user::where('role','!=','admin')->get();



        return view('front.report',compact('data','cost','profit','resellers'));
    }
}
