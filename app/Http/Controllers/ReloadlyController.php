<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RechargeHistory;
use App\Services\ReloadlyProvider;
use Auth as a;


class ReloadlyController extends Controller
{
    //

    protected $reloadly;
    public function __construct(ReloadlyProvider $reloadly)
    {
        $this->reloadly = $reloadly;
    }

    public function index()
    {

        $stage = 'initial';
        if(a::user()->role == 'user'){
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type','International')->latest()->take(10)->get();
        }else{
            $data = RechargeHistory::where('type','International')->join('users','users.id','=','recharge_histories.reseller_id')
            ->select('recharge_histories.*','users.nationality')
            ->latest()
            ->take(10)
            ->get();


        }

        return view('front.recharge-reloadly',compact('stage','data'));
    }

    public function mobile_number_details(Request $request)
    {
        $change = [' ','+'];
        $number = str_replace($change,'',$request->number);
        $countryIso = $request->countryIso;
        //$number = $request->number;
        $data = $this->reloadly->operator_details($number,$countryIso);
        $data = (array) $data;
        //echo $data;
        return $data;
       //file_put_contents('test.txt',$data->id);

       // file_put_contents('test.txt',$number." ".$countryIso);
    }
}
