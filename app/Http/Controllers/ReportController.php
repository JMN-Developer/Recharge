<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RechargeHistory;
use Auth as a;
use  App\Charts\InternationalRechargeChart;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;

class ReportController extends Controller
{
    //
    public function index(InternationalRechargeChart $chart)
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

        $international_chart = $chart->build();

        //file_put_contents('test.txt',$international_chart->cdn());

        return view('front.report',compact('data','cost','profit','resellers'));
    }

    public function get_report_data(InternationalRechargeChart $chart)
    {
        $international_chart = $chart->build();
        $international_chart_container='';
        $international_chart_container.=$international_chart->container();
        $international_chart_script = '';
        $international_chart_script.=$international_chart->script();
        $data = [];
       array_push($data,['international_container'=>$international_chart_container,'international_script'=>$international_chart_script]);
        file_put_contents('test.txt',json_encode($data)." ".$international_chart_container);
        echo json_encode($data) ;
      //  echo $international_chart->container();
        //return json_encode($international_chart);
        //file_put_contents('test.txt',$international_chart->container());
        //return json_encode($international_chart);
        //echo $international_chart->container();
    }
}
