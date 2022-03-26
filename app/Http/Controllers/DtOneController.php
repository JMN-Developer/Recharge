<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RechargeHistory;
use App\Services\DtOneProvider;
use Illuminate\Support\Facades\Auth as a;
use App\Services\CheckRechargeAvail;
use App\Services\UpdateWallet;
use App\Services\BangladeshiRecharge;
use App\Services\GenerateTransactionId;
use App\Models\Balance;
use Illuminate\Support\Facades\Log;
use Auth;
class DtOneController extends Controller
{
    //
    protected $dtone;
    private $bangladeshi_recharge;
    public function __construct()
    {
        $dtone = new DtOneProvider();
        $this->dtone = $dtone;
        $this->bangladeshi_recharge = new BangladeshiRecharge();
    }
    public function index(){

        if(a::user()->role == 'user'){
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where(function($q) {
                $q->where('type','International')
                  ->orWhere('type','Bangladesh');
            })->latest()->take(5)->get();
           // $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type','International')->latest()->take(5)->get();
        }else{
            $data = RechargeHistory::where(function($q) {
                $q->where('type','International')
                  ->orWhere('type','Bangladesh');
            })->join('users','users.id','=','recharge_histories.reseller_id')
            ->select('recharge_histories.*','users.nationality')
            ->latest()
            ->take(5)
            ->get();


        }

        return view('front.recharge-dtone',compact('data'));
    }
    function make_sku_list($skus,$number)
    {

        $data = array();
        foreach($skus as $sku)
        {
            if(str_contains($number,'+880')){
                $rate = euro_rate_for_bd_recharge();
                $unit_rate = $rate/100;
                $value = $sku->destination->amount;
                $updated_value = $value*$unit_rate;
                $amount_text =  $updated_value." Euro &nbsp(&nbsp" .$sku->name." will be received )";
                array_push($data,['skuId'=>$sku->id,'amount'=>$updated_value,'amount_text'=>$amount_text,'bd_amount'=>$sku->destination->amount]);
            }
            else{
                $amount_text = $sku->prices->retail->amount+reseller_comission($sku->prices->retail->amount)."Euro &nbsp(&nbsp" .$sku->name." will be received )";
                array_push($data,['skuId'=>$sku->id,'amount'=> $sku->prices->retail->amount+reseller_comission($sku->prices->retail->amount),'amount_text'=>$amount_text,'bd_amount'=>$sku->destination->amount]);
            }
            //$total_amount = floor($sku->minAmount * $sku->exchangeRate);
           

           
        }
        usort($data, function($a, $b) {
              return $a['amount'] <=> $b['amount'];
             });
        return $data;
    }
    public function all_transaction()
    {
        $data = $this->dtone->transaction_list();
        $record = [];
        foreach($data as $d)
        {
            if(str_contains($d->creation_date,'2022-03-04') || str_contains($d->creation_date,'2022-03-05') || str_contains($d->creation_date,'2022-03-06'))
            array_push($record,['date'=>$d->creation_date,'external_id'=>$d->external_id,'amount'=>$d->prices->retail->amount]);
        }
        return json_encode($record);
    }

    public function make_bundle_data_list($list)
    {
        $data = array();
        foreach($list as $l)
        {
            array_push($data,['description'=>$l->description,'skuId'=>$l->id,'amount'=>round($l->prices->retail->amount+reseller_comission($l->prices->retail->amount),2),'validity'=>$l->validity->quantity.' '.$l->validity->unit]);
        }
        usort($data, function($a, $b) {
            return $a['amount'] <=> $b['amount'];
           });
        return $data;
    }

    public function make_internet_data_list($list)
    {
        $data = array();
        foreach($list as $l)
        {
            array_push($data,['description'=>$l->description,'skuId'=>$l->id,'amount'=>round($l->prices->retail->amount+reseller_comission($l->prices->retail->amount),2),'validity'=>$l->validity->quantity.' '.$l->validity->unit]);
        }
        usort($data, function($a, $b) {
            return $a['amount'] <=> $b['amount'];
           });
        return $data;
    }
    public function mobile_number_details(Request $request)
    {

        $number = $request->number;

        $countryIso = $request->countryIso;
        
        //$number = $request->number;
        $data = $this->dtone->lookup($number);


        if($data['status']){
            $data = $data['payload'];
            $credit_data = [];
            $internet_data = [];
            $combo_data = [];
            for($i=0;$i<sizeof($data);$i++)
            {
                if($data[$i]->tags[0] =='AIRTIME')
                array_push($credit_data,$data[$i]);
                if($data[$i]->tags[0] =='BUNDLE')
                array_push($combo_data,$data[$i]);
                if($data[$i]->tags[0] =='DATA')
                array_push($internet_data,$data[$i]);

            }
           // file_put_contents('test.txt',json_encode($credit_data));
            $skus = $this->make_sku_list($credit_data,$number);
            $internet_data = $this->make_internet_data_list($internet_data);
            $combo_data = $this->make_bundle_data_list($combo_data);
            $operator_name = $data[0]->operator->name;
            return ['status'=>true,'data'=>$data,'operator_name'=>$operator_name,'skus'=>$skus,'internet'=>$internet_data,'combo'=>$combo_data];
        }
        else
        {

            return ['status'=>false,'message'=>$data['payload']->errors[0]->message];
        }
        //$data = (array) $data;
        //echo $data;
       // return $data;
       //file_put_contents('test.txt',$data->id);

       // file_put_contents('test.txt',$number." ".$countryIso);
    }


    public function create_recharge($data,$number,$txid,$country_code,$service = 0)
    {
        $discount =$data->prices->retail->amount - $data->prices->wholesale->amount;
        $total_commission = reseller_comission($data->prices->retail->amount);
        $reseller_profit = reseller_profit($data->prices->retail->amount+$total_commission);
        $admin_profit = ($data->prices->retail->amount+$total_commission)-$reseller_profit-$data->prices->wholesale->amount;
        //$admin_profit = $total_commission-$reseller_profit;
        $log_data = 'Number = '.$number.' Amount = '.$data->prices->retail->amount+$total_commission.' R-Com = '.$reseller_profit.' A-Com = '.$admin_profit.' TXID = '.$txid;
        Log::channel('rechargelog')->info($log_data);
        $recharge = RechargeHistory::create([
            'reseller_id'=>a::user()->id,
            'number'=>$number,
            'amount'=>$data->prices->retail->amount+$total_commission,
            'txid'=>$txid,
            'type'=>'International',
            'operator'=>$data->product->operator->name,
            'status'=>'completed',
            'cost'=>round($data->prices->wholesale->amount,2),
            'transaction_id_company'=>$data->id,
            'country_code'=>$country_code,
            'discount'=>$discount,
            'service'=>$service,
            'reseller_com'=>$reseller_profit,
            'admin_com'=>$admin_profit,
            'deliveredAmount'=>floor($data->benefits[0]->amount->total_excluding_tax),
            'deliveredAmountCurrencyCode'=>$data->benefits[0]->unit,
            'company_name'=>'International4'

        ]);
        return $recharge;
    }
    public function create_recharge_bangladesh($number,$amount,$updated_amount,$txid,$operator_name,$transaction_id_company,$service=0)
    {

        $admin_profit = $this->calculate_profit($amount);
        $cost = $amount - $admin_profit;
       // $log_data = 'Number = '.$number.' Amount = '.$amount.' R-Com = '.$service.' A-Com = '.$admin_profit.' TXID = '.$txid;
       // Log::channel('rechargelog')->info($log_data);
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
    public function update_balance($recharge_amount,$cost)
    {
        $balance_info = Balance::where('type','dtone')->first();
        $current_balance = $balance_info->balance-$cost;
        Balance::where('type','dtone')->update(['balance'=>$current_balance]);

    }
    public function update_balance_bangladesh()
    {
        $current_balance =$this->bangladeshi_recharge->balanceInfo();

        Balance::where('type','ssl')->update(['balance'=>$current_balance['balance_info']]);

    }

    public function recharge(Request $request)
    {
        //file_put_contents('test.txt',$request->amount);
        //file_put_contents('test.txt',$test);
        if(!CheckRechargeAvail::check($request->amount,'International'))
        {
            return ['status'=>false,'message'=>'Insufficient wallet & Limit. Please contact with admin'];
        }

        $country_code = $request->countryCode;
       // file_put_contents('test.txt',$country_code);
        //return;
        $number = $request->number;
      //  file_put_contents('test.txt',$request->bd_amount);
        //return;
        if(str_contains($number,'+880') && $request->bd_amount!='undefined')
        {
            $change = [' ','+'];
            $number = str_replace($change,'',$number);
            $operator_details =  $this->bangladeshi_recharge->operatorInfo($number);
            if($operator_details['soap_exception_occured']==false)
            {
                $operator_id = $operator_details['data']->operator_id;
                $operator_name =  $operator_details['data']->operator_name;
                $guid =  new GenerateTransactionId(Auth::user()->id,13);
                $guid =  $guid->transaction_id();
                $create_recharge = $this->bangladeshi_recharge->CreateRecharge($guid,$operator_id,$number,$request->bd_amount);
                if($create_recharge['data']->recharge_status=='100')
                {
                $init_recharge =  $this->bangladeshi_recharge->InitRecharge($guid,$create_recharge['data']->vr_guid);
                 if($init_recharge['data']->recharge_status == 200)
                 {
                  $recharge = $this->create_recharge_bangladesh($number,$request->amount,$request->bd_amount,$guid,$operator_name,$create_recharge['data']->vr_guid,$request->service_charge);
                  UpdateWallet::update($recharge);
                   $this->update_balance_bangladesh();
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
            else
            {
                return ['status'=>false,'message'=>$operator_details['exception']];
            }
            
           // file_put_contents('test.txt',$request->bd_amount);
            

        }
        $skuId = $request->id;
        $transaction =  new GenerateTransactionId(a::user()->id,12);
        $txid = $transaction->transaction_id();
        $data = $this->dtone->recharge($skuId,$txid,$number);

    //  //file_put_contents('test.txt',$tmp_data->responseCode);

        if($data['status']){

          $recharge = $this->create_recharge($data['payload'],$number,$txid,$country_code,$request->service_charge);
         UpdateWallet::update($recharge);
          $this->update_balance($data['payload']->prices->retail->amount,$data['payload']->prices->wholesale->amount);
        return ['status'=>true,'message'=>'Recharge Successfull'];
        }
    else
    {

        return ['status'=>false,'message'=>$data['message']];
    }


    }
}
