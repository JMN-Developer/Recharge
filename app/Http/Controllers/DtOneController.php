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
            $amount_text = $sku->prices->retail->amount." Euro (".$sku->name." will receive)";

            array_push($data,['skuId'=>$sku->id,'amount'=> $sku->source->amount,'amount_text'=>$amount_text]);
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

            return ['status'=>false,'message'=>$data['payload']->message];
        }
        //$data = (array) $data;
        //echo $data;
       // return $data;
       //file_put_contents('test.txt',$data->id);

       // file_put_contents('test.txt',$number." ".$countryIso);
    }

    public function recharge(Request $request)
    {

        //file_put_contents('test.txt',$test);
        if(!CheckRechargeAvail::check($request->amount))
        {
            return ['status'=>false,'message'=>'Insufficient wallet & Limit. Please contact with admin'];
        }


        $number = $request->number;

        $skuId = $request->id;
        $transaction =  new GenerateTransactionId(a::user()->id,12);
        $txid = $transaction->transaction_id();
       $data = $this->dtone->recharge($skuId,$txid,$number);


    // $tmp_data = json_decode($tmp_data);
    //  $data = ['status'=>true,'payload'=>$tmp_data];
    //  //file_put_contents('test.txt',$tmp_data->responseCode);

    //     if($data['status']){
    //        // file_put_contents('test.txt',$data['payload']);
    //      UpdateWallet::update($data['payload']->payLoad->faceValue,$data['payload']->payLoad->invoiceAmount);
    //       $this->create_recharge($data['payload']->payLoad,$number,$txid,$country_code);
    //       $this->update_balance($data['payload']->payLoad->faceValue,$data['payload']->payLoad->invoiceAmount);
        return ['status'=>true,'message'=>'Recharge Successfull'];
    //     }
    // else
    // {
    //     $data = $data['payload'];
    //     return ['status'=>false,'message'=>$data->message];
    // }
      //  file_put_contents('test.txt',json_encode($data));
       // file_put_contents('test.txt',$request->operatorId." ".$request->amount." ".$request->countryCode." ".$request->number);

    }
}
