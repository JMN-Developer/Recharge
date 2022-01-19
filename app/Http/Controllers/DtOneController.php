<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RechargeHistory;
use App\Services\DtOneProvider;
use Illuminate\Support\Facades\Auth as a;
use App\Services\CheckRechargeAvail;
use App\Services\UpdateWallet;
use App\Services\GenerateTransactionId;
use App\Models\Balance;

class DtOneController extends Controller
{
    //
    protected $dtone;
    public function __construct()
    {
        $dtone = new DtOneProvider();
        $this->dtone = $dtone;
    }
    public function index(){

        if(a::user()->role == 'user'){
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type','International')->latest()->take(10)->get();
        }else{
            $data = RechargeHistory::where('type','International')->join('users','users.id','=','recharge_histories.reseller_id')
            ->select('recharge_histories.*','users.nationality')
            ->latest()
            ->take(10)
            ->get();


        }

        return view('front.recharge-dtone',compact('data'));
    }
    function make_sku_list($skus)
    {

        $data = array();
        foreach($skus as $sku)
        {
            //$total_amount = floor($sku->minAmount * $sku->exchangeRate);
            $amount_text = $sku->prices->retail->amount+reseller_comission($sku->prices->retail->amount)."</p> Euro &nbsp(&nbsp" .$sku->name." will be received )";

            array_push($data,['skuId'=>$sku->id,'amount'=> $sku->prices->retail->amount+reseller_comission($sku->prices->retail->amount),'amount_text'=>$amount_text]);
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
            $skus = $this->make_sku_list($data);
            $operator_name = $data[0]->operator->name;
            // $operator_logo = $this->ppn->operator_logo($data->payLoad->productId);
            // if($operator_logo['status'])
            // {
            //     $logo_url = $operator_logo['payload']->payLoad[0]->imageUrl;
            // }
            // else
            // {
            //     $logo_url = '';
            // }
            //file_put_contents('test.txt',json_encode($skus));

            return ['status'=>true,'data'=>$data,'operator_name'=>$operator_name,'skus'=>$skus];
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
        $reseller_profit = reseller_profit($total_commission);
        $admin_profit = $total_commission-$reseller_profit;
        $recharge = RechargeHistory::create([
            'reseller_id'=>a::user()->id,
            'number'=>$number,
            'amount'=>$data->prices->retail->amount+reseller_comission($data->prices->retail->amount),
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

    public function update_balance($recharge_amount,$cost)
    {
        $balance_info = Balance::where('type','dtone')->first();
        $current_balance = $balance_info->balance-$cost;
        Balance::where('type','dtone')->update(['balance'=>$current_balance]);

    }

    public function recharge(Request $request)
    {
        //file_put_contents('test.txt',$request->amount);
        //file_put_contents('test.txt',$test);
        if(!CheckRechargeAvail::check($request->amount))
        {
            return ['status'=>false,'message'=>'Insufficient wallet & Limit. Please contact with admin'];
        }

        $country_code = $request->countryCode;
        $number = $request->number;

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
