<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SoapClient;
use App\Services\BangladeshiRecharge;
use App\Services\GenerateTransactionId;
use App\Services\UpdateWallet;
use App\Models\RechargeHistory;
use DB;
use Auth;
use App\Models\Balance;
use Illuminate\Support\Facades\Log;
class BangladeshRechargeController extends Controller
{
    //
    private $bangladeshi_recharge;
    public function __construct()
    {
        $this->bangladeshi_recharge = new BangladeshiRecharge();
    }
    public function check_balance()
    {
        $balance = $this->bangladeshi_recharge->balanceInfo();
        dd($balance);
    }

    public function calculate_profit($amount)
    {
        $rate = euro_rate_for_bd_recharge();
        $current_currency_rate = $this->bangladeshi_recharge->current_euro_rate();
        $currency_profit = $rate - (100/$current_currency_rate);
        $api_profit = (1.5/100)*$amount;
       // file_put_contents('test.txt',$current_currency_rate.' '.$currency_profit.' '.$company_profit);
        $admin_profit = $currency_profit+$api_profit;
        return $admin_profit;
    }

    public function create_recharge($number,$amount,$updated_amount,$txid,$operator_name,$transaction_id_company,$service=0)
    {

        $admin_profit = $this->calculate_profit($amount);
        $cost = $amount - $admin_profit;
        $log_data = 'Number = '.$number.' Amount = '.$amount.' R-Com = '.$service.' A-Com = '.$admin_profit.' TXID = '.$txid;
        Log::channel('rechargelog')->info($log_data);
        $recharge = RechargeHistory::create([
            'reseller_id'=>Auth::user()->id,
            'number'=>$number,
            'amount'=>$amount,
            'txid'=>$txid,
            'type'=>'Bangladesh',
            'operator'=>$operator_name,
            'status'=>'completed',
            'cost'=>$cost,
            'transaction_id_company'=>$transaction_id_company,
            'service'=>$service,
            'admin_com'=>$admin_profit,
            'deliveredAmount'=>$updated_amount,
            'deliveredAmountCurrencyCode'=>'BDT',
            'company_name'=>'Bangladesh1'

        ]);
        return $recharge;
    }
    public function update_balance()
    {
        $current_balance =$this->bangladeshi_recharge->balanceInfo();

        Balance::where('type','ssl')->update(['balance'=>$current_balance['balance_info']]);

    }
    public function query_recharge(Request $request)
    {
        $transaction_id = $request->transaction_id;
        $transaction_id_company = RechargeHistory::where('txid',$transaction_id)->first()->transaction_id_company;
        $data = $this->bangladeshi_recharge->query_recharge($transaction_id,$transaction_id_company);
        return json_encode($data);
    }

    public function recharge(Request $request)
    {
        //$amount = $request->amount;
    //    $unit_rate = 100/$rate;
        $bd_amount = $request->bd_amount;// $unit_rate*$amount;
        $rate = euro_rate_for_bd_recharge();
        $unit_rate = $rate/100;
        $amount = round($bd_amount*$unit_rate,3);
        //file_put_contents('test.txt',$amount.' '.$bd_amount);
        //return;
       $change = [' ','+'];
        $msisdn = str_replace($change,'',$request->number);
        $operator_id = $request->operator_id;
        //file_put_contents('test.txt',$operator_id);
        $guid =  new GenerateTransactionId(Auth::user()->id,13);
        $guid =  $guid->transaction_id();
        //file_put_contents('test.txt',$amount.' '.$bd_amount);
        //return;
        //file_put_contents('test.txt',$request->operator_id);
      $create_recharge = $this->bangladeshi_recharge->CreateRecharge($guid,$operator_id,$msisdn,$bd_amount);

      if($create_recharge['data']->recharge_status=='100')
      {
      $init_recharge =  $this->bangladeshi_recharge->InitRecharge($guid,$create_recharge['data']->vr_guid);
       if($init_recharge['data']->recharge_status == 200)
       {
        $recharge = $this->create_recharge($msisdn,$request->amount,$bd_amount,$guid,$request->operator_name,$create_recharge['data']->vr_guid,$request->service_charge);
        UpdateWallet::update($recharge);
         $this->update_balance();
        return ['status'=>true,'message'=>'Recharge Successfull'];
       }
       else
       {
        return ['status'=>false,'message'=>$init_recharge['data']->message];
       }
    }
    else
    {
        return ['status'=>false,'message'=>$create_recharge['data']->message];
    }

    }

    public function bangladeshi_exchange_rate(Request $request){


       $rate = euro_rate_for_bd_recharge();
       $unit_rate = $rate/100;
       $value = $request->value;

        $updated_value = $value*$unit_rate;
        //file_put_contents('test.txt',$unit_rate." ".$value." ".$updated_value);
        echo $updated_value;

    }

    public function index()
    {
        //dd($this->bangladeshi_recharge->balanceInfo());
        if(Auth::user()->role != 'admin'){
            $data = RechargeHistory::where('reseller_id', Auth::user()->id)->where('type','Bangladesh')->latest()->take(10)->get();
        }else{
            $data = RechargeHistory::where('type','Bangladesh')->join('users','users.id','=','recharge_histories.reseller_id')
            ->select('recharge_histories.*','users.nationality')
            ->latest()
            ->take(10)
            ->get();


        }
        return view('front.recharge-bangladesh',compact('data'));
      //
     //echo json_encode($this->bangladeshi_recharge->getOperatorLimit('3'));
      //dd( $this->bangladeshi_recharge->operatorInfo('8801845318609'));


    }
    public function mobile_number_details(Request $request)
    {
       // $msisdn = $request->number;
    //    $current_currency_rate = $this->bangladeshi_recharge->current_euro_rate();
    //    file_put_contents('test.txt',$current_currency_rate);
        $change = [' ','+'];
        $msisdn = str_replace($change,'',$request->number);
        $operator_details =  $this->bangladeshi_recharge->operatorInfo($msisdn);
        $rate = euro_rate_for_bd_recharge();
        $unit_rate = $rate/100;

        if($operator_details['soap_exception_occured']==false)
        {
            $rate = euro_rate_for_bd_recharge();
            $unit_rate = $rate/100;
            $data =  $this->bangladeshi_recharge->offer_details($operator_details['data']->operator_id);
           // file_put_contents('test.txt',$operator_details['data']->operator_name);
            foreach($data as $d)
            {
                $d->update_amount =  round($d->amount*$unit_rate,4);
                $d->operator_logo = DB::table('sim_operators')->where('operator',$operator_details['data']->operator_name)->first()->img;
            }
           // file_put_contents('test.txt',json_encode($data));

            return ['status'=>true,'offer_data'=>$data,'operator_id'=>$operator_details['data']->operator_id,'operator_name'=>$operator_details['data']->operator_name,'exchange_rate'=>$unit_rate];
        }
        else
        {
            return ['status'=>false,'message'=>$operator_details['exception']];
        }

    }
    public function verify_msisdn()
    {
        $msisdn = '8801845318609';
        $this->bangladeshi_recharge->verifyMsisdn($msisdn);
    }
}
