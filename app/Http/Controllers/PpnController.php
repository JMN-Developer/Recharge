<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use Illuminate\Http\Request;
use App\Models\RechargeHistory;
use App\Models\User;
use App\Services\PrePayProvider;
use Auth as a;
use App\Services\GenerateTransactionId;

class PpnController extends Controller
{
    //

    protected $ppn;
    public function __construct(PrePayProvider $ppn)
    {
        $this->ppn = $ppn;
    }
    public function index()
    {

        if(a::user()->role == 'user'){
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type','International')->where('company_name','Ppn')->latest()->take(10)->get();
        }else{
            $data = RechargeHistory::where('type','International')->where('company_name','Ppn')->join('users','users.id','=','recharge_histories.reseller_id')
            ->select('recharge_histories.*','users.nationality')
            ->latest()
            ->take(10)
            ->get();


        }

        return view('front.recharge-ppn',compact('data'));
    }

    function make_sku_list($skus)
    {
        $data = array();
        foreach($skus as $sku)
        {
            $total_amount = floor($sku->minAmount * $sku->exchangeRate);
            $amount_text = $sku->minAmount." Euro (".$total_amount." ".$sku->currencyCode." will receive)";

            array_push($data,['skuId'=>$sku->skuId,'amount'=>$sku->minAmount,'amount_text'=>$amount_text]);
        }
        usort($data, function($a, $b) {
              return $a['amount'] <=> $b['amount'];
             });
        return $data;
    }

    public function mobile_number_details(Request $request)
    {
        $change = [' ','+'];
        $number = str_replace($change,'',$request->number);
        $countryIso = $request->countryIso;
        //$number = $request->number;
        $data = $this->ppn->lookup($number);
        if($data['status']){
            $data = $data['payload'];
            $skus = $this->make_sku_list($data->payLoad->skus);
            $operator_logo = $this->ppn->operator_logo($data->payLoad->productId);
            if($operator_logo['status'])
            {
                $logo_url = $operator_logo['payload']->payLoad[0]->imageUrl;
            }
            else
            {
                $logo_url = '';
            }
            //file_put_contents('test.txt',json_encode($skus));
            return ['status'=>true,'data'=>$data,'skus'=>$skus,'logo_url'=>$logo_url,'operator_name'=>$data->payLoad->operator];
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

    public function create_recharge($data,$number,$txid,$country_code)
    {
        $discount = $data->faceValue - $data->invoiceAmount;
        $reseller_com = $discount/2;
        $admin_com = $discount-$reseller_com;
        RechargeHistory::create([
            'reseller_id'=>a::user()->id,
            'number'=>$number,
            'amount'=>$data->faceValue,
            'txid'=>$txid,
            'type'=>'International',
            'operator'=>$data->product->productName,
            'status'=>'completed',
            'cost'=>round($data->invoiceAmount,2),
            'transaction_id_company'=>$data->transactionId,
            'country_code'=>$country_code,
            'discount'=>$discount,
            'reseller_com'=>$reseller_com,
            'admin_com'=>$admin_com,
            'deliveredAmount'=>floor($data->topupDetail->localCurrencyAmount),
            'deliveredAmountCurrencyCode'=>$data->topupDetail->destinationCurrency,
            'company_name'=>'Ppn'

        ]);
    }

    public function update_balance($recharge_amount,$cost)
    {
        $balance_info = Balance::where('type','ppn')->first();
        $current_balance = $balance_info->balance-$cost;
        $discount = $recharge_amount-$cost;
        $total_cost_reseller = $cost+($discount/2);
        Balance::where('type','ppn')->update(['balance'=>$current_balance]);
        if(a::user()->role != 'admin')
        {
            $existing_wallet = User::where('id',a::user()->id)->first()->wallet;
            $new_wallet = $existing_wallet-$total_cost_reseller;
            User::where('id',a::user()->id)->update(['wallet'=>$new_wallet]);

        }
    }

    public function recharge(Request $request)
    {
        $change = [' ','+'];
        $country_code = $request->countryCode;
        $number = str_replace($change,'',$request->number);
        $amount = $request->amount;
        $skuId = $request->skuId;
        $transaction =  new GenerateTransactionId(a::user()->id,12);
        $txid = $transaction->transaction_id();
        $data = $this->ppn->recharge($skuId,$amount,$txid,$number);
    //    $tmp_data = '{
    //     "responseCode":"000",
    //     "responseMessage":null,
    //     "payLoad":{
    //        "transactionId":128959196,
    //        "transactionDate":"12/9/2021 05:09",
    //        "invoiceAmount":1.680,
    //        "faceValue":2.00,
    //        "discount":0.0,
    //        "fee":0.0,
    //        "product":{
    //           "skuId":3612,
    //           "productName":"Robi-Bangladesh",
    //           "faceValue":2.00,
    //           "instructions":null
    //        },
    //        "topupDetail":{
    //           "localCurrencyAmount":144.32,
    //           "salesTaxAmount":0.00,
    //           "localCurrencyAmountExcludingTax":144.32,
    //           "destinationCurrency":"BDT",
    //           "operatorTransactionId":null
    //        },
    //        "pins":null,
    //        "giftCardDetail":null,
    //        "simInfo":null,
    //        "billPaymentDetail":null
    //     }
    //  }';
    //  $tmp_data = json_decode($tmp_data);
    //  $data = ['status'=>true,'payload'=>$tmp_data];
     //file_put_contents('test.txt',$tmp_data->responseCode);

        if($data['status']){
           // file_put_contents('test.txt',$data['payload']);
          $this->create_recharge($data['payload']->payLoad,$number,$txid,$country_code);
          $this->update_balance($data['payload']->payLoad->faceValue,$data['payload']->payLoad->invoiceAmount);
        return ['status'=>true,'message'=>'Recharge Successfull'];
        }
    else
    {
        $data = $data['payload'];
        return ['status'=>false,'message'=>$data->message];
    }
      //  file_put_contents('test.txt',json_encode($data));
       // file_put_contents('test.txt',$request->operatorId." ".$request->amount." ".$request->countryCode." ".$request->number);

    }

}
