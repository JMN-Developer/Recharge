<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RechargeHistory;
use Auth as a;
use  App\Charts\LineChart;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;
use Illuminate\Support\Carbon;
use DB;
use App\Models\sim;
use App\Models\Order;
use phpDocumentor\Reflection\Types\Null_;

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

       // $international_chart = $chart->build();

        //file_put_contents('test.txt',$international_chart->cdn());

        return view('front.report',compact('data','cost','profit','resellers'));
    }
    public function make_chart($sales,$profits,$date)
    {
        $chart = new LineChart($sales,$profits,$date);
        $chart = $chart->build();
        $chart_container='';
        $chart_container.=$chart->container().''.$chart->script();
        return $chart_container;
    }

    public function data_fetch($type,$start_date,$end_date,$service)
    {

    if($type=='all')
     $datas = RechargeHistory::whereBetween('created_at', [$start_date, $end_date])->select(DB::raw('DATE(created_at) as date'),DB::raw('format(sum(cost),2) as sales'),DB::raw('format(sum(admin_com)+sum(discount),2) as profit'))->groupBy('date')->get();
    else
    {
       if($service == 'recharge') 
       {
      
        $datas = RechargeHistory::where('type',$type)->whereBetween('created_at', [$start_date, $end_date])->select(DB::raw('DATE(created_at) as date'),DB::raw('format(sum(cost),2) as sales'),DB::raw('format(sum(admin_com)+sum(discount),2) as profit'))->groupBy('date')->get();
       
       }
       else if($service == 'sim')
       {
        $datas = Sim::whereBetween('created_at', [$start_date, $end_date])->select(DB::raw('DATE(created_at) as date'),DB::raw('format(sum(original_price),2) as sales'),DB::raw('format(sum(buy_price),2) as profit'))->groupBy('date')->get();
       }
       else if($service =='cargo')
       {
        $datas = Order::whereBetween('created_at', [$start_date, $end_date])->select(DB::raw('DATE(created_at) as date'),DB::raw('format(sum(cost),2) as sales'),DB::raw('format(sum(total)-sum(addiCharge),2) as profit'))->groupBy('date')->get();
       }
    }
        $sale_data = [];
        $profit_data = [];
        $date_data = [];
        foreach($datas as $d)
        {
            $sale_data[]=$d->sales;
            $profit_data[] = $d->profit;
            $date_data[] = $d->date;
        }
        return ['sales'=>$sale_data,'profits'=>$profit_data,'date'=>$date_data];
    }

    public function get_report_data_separate(Request $request)
    {
        $start_date =  Carbon::parse($request->start_date)->toDateTimeString();
        $end_date =  Carbon::parse($request->end_date)->addDays(1)->toDateTimeString();
       // file_put_contents('test.txt',$request->type);
        if($request->type=='all')
        {
            $chart_data = $this->data_fetch('all',$start_date,$end_date,'recharge');
            $all_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
            echo json_encode(['chart_container'=>$all_chart,'type'=>'all']);
            
        }
       else if($request->type=='international_recharge')
        {
            $chart_data = $this->data_fetch('International',$start_date,$end_date,'recharge');
            $all_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
            echo json_encode(['chart_container'=>$all_chart,'type'=>'international_recharge']);
            
        }

        else if($request->type=='domestic_recharge')
        {
            $chart_data = $this->data_fetch('Domestic',$start_date,$end_date,'recharge');
            //file_put_contents('test.txt',json_encode($chart_data));
            $all_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
            echo json_encode(['chart_container'=>$all_chart,'type'=>'domestic_recharge']);
            
        }

        else if($request->type=='pin')
        {
            $chart_data = $this->data_fetch('Pin',$start_date,$end_date,'recharge');
            $all_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
            echo json_encode(['chart_container'=>$all_chart,'type'=>'pin']);
            
        }

        else if($request->type=='white_calling')
        {
            $chart_data = $this->data_fetch('White Calling',$start_date,$end_date,'recharge');
            $all_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
            echo json_encode(['chart_container'=>$all_chart,'type'=>'white_calling']);
            
        }
        else if($request->type=='sim')
        {
            $chart_data = $this->data_fetch('sim',$start_date,$end_date,'sim');
            $all_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
            echo json_encode(['chart_container'=>$all_chart,'type'=>'sim']);
            
        }
        else if($request->type=='cargo')
        {
            $chart_data = $this->data_fetch('cargo',$start_date,$end_date,'cargo');
            $all_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
            echo json_encode(['chart_container'=>$all_chart,'type'=>'cargo']);
            
        }
    
    }

    public function get_report_data(Request $request)
    {
      
        $start_date =  Carbon::parse($request->start_date)->toDateTimeString();
        $end_date =  Carbon::parse($request->end_date)->addDays(1)->toDateTimeString();
      
      $chart_data = $this->data_fetch('International',$start_date,$end_date,'recharge');
      $international_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
      $international_sale =round(array_sum($chart_data['sales']),2) ;
      $international_profit =round(array_sum($chart_data['profits']),2) ;

      $chart_data = $this->data_fetch('Domestic',$start_date,$end_date,'recharge');   
      $domestic_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
      $domestic_sale =round(array_sum($chart_data['sales']) ,2);
      $domestic_profit =round(array_sum($chart_data['profits']),2) ;


      $chart_data = $this->data_fetch('Pin',$start_date,$end_date,'recharge');
      $pin_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
      $pin_sale =round(array_sum($chart_data['sales']),2);
      $pin_profit = round(array_sum($chart_data['profits']),2);

      $chart_data = $this->data_fetch('White Calling',$start_date,$end_date,'recharge');
      $white_calling_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
      $white_calling_sale =round(array_sum($chart_data['sales']),2);
      $white_calling_profit =round(array_sum($chart_data['profits']),2);

      $chart_data = $this->data_fetch('all',$start_date,$end_date,'recharge');
      $all_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
      $all_sale =round(array_sum($chart_data['sales']),2);
      $all_profit =round(array_sum($chart_data['profits']),2);

      $chart_data = $this->data_fetch('sim',$start_date,$end_date,'sim');
      $sim_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
      $sim_sale =round(array_sum($chart_data['sales']),2);
      $sim_profit =round(array_sum($chart_data['profits']),2);

      $chart_data = $this->data_fetch('cargo',$start_date,$end_date,'cargo');
      $cargo_chart = $this->make_chart($chart_data['sales'],$chart_data['profits'],$chart_data['date']);
      $cargo_sale =round(array_sum($chart_data['sales']),2);
      $cargo_profit =round(array_sum($chart_data['profits']),2);


        //file_put_contents('test.txt',$international_profit);


       
        $data = ['international_container'=>$international_chart,
        'domestic_container'=>$domestic_chart,
        'pin_container'=>$pin_chart,
        'white_calling_container'=>$white_calling_chart,
        'all_container'=>$all_chart,
        'international_sale'=>$international_sale,
        'international_profit'=>$international_profit,
        'domestic_sale'=>$domestic_sale,
        'domestic_profit'=>$domestic_profit,
        'pin_sale'=>$pin_sale,
        'pin_profit'=>$pin_profit,
        'white_calling_sale'=>$white_calling_sale,
        'white_calling_profit'=>$white_calling_profit,
        'all_sale'=>$all_sale,
        'all_profit'=>$all_profit,
        'sim_container'=>$sim_chart,
        'cargo_container'=>$cargo_chart,
        'sim_sale'=>$sim_sale,
        'sim_profit'=>$sim_profit,
        'cargo_sale'=>$cargo_sale,
        'cargo_profit'=>$cargo_profit

    
    ];
      // array_push($data,);
        
        echo json_encode($data) ;
      
    }
}
