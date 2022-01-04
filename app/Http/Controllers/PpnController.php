<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use Illuminate\Http\Request;
use App\Models\RechargeHistory;
use App\Models\User;
use App\Notifications\PinSentToEmail;
use App\Services\CheckRechargeAvail;
use App\Services\PrePayProvider;
use Auth as a;
use App\Services\GenerateTransactionId;
use App\Services\UpdateWallet;
use Illuminate\Support\Facades\Notification;




class PpnController extends Controller
{
    //

    protected $ppn;
    public function __construct()
    {
        $ppn = new PrePayProvider();
        $this->ppn = $ppn;
    }
    public function index()
    {

        if(a::user()->role == 'user'){
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type','International')->latest()->take(10)->get();
        }else{
            $data = RechargeHistory::where('type','International')->join('users','users.id','=','recharge_histories.reseller_id')
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
            $amount_text = $sku->minAmount+reseller_comission($sku->minAmount)." Euro (".$total_amount." ".$sku->currencyCode." will receive)";
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
            if($data->responseMessage=='Success')
            {
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
                return ['status'=>false,'message'=>'Error in mobile number'];
            }
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
    public function send_pin(Request $request)
    {
       // file_put_contents('test.txt',$request->pin_number." ".$request->email);
       // $data = User::find(a::user()->id)->first();
        $data['email'] = $request->email;
        $PinData = [
            'from'=>'pointrecharge@gmail.com',
            'pin'=>$request->pin_number
        ];
        try {
            Notification::route('mail',$request->email)
                ->notify(new PinSentToEmail($PinData));
           // Notification::send('kazinokib7@gmail.com', new PinSentToEmail($PinData));
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    public function create_recharge($data,$number,$txid,$country_code,$service)
    {
        // $discount = $data->faceValue - $data->invoiceAmount;
        // $reseller_com = reseller_comission($discount);
        // $admin_com = $discount-$reseller_com;

        $discount = $data->faceValue - $data->invoiceAmount;
        $total_commission = reseller_comission($data->faceValue);
        $reseller_profit = reseller_profit($total_commission);
        $admin_profit = $total_commission-$reseller_profit;
        $recharge = RechargeHistory::create([
            'reseller_id'=>a::user()->id,
            'number'=>$number,
            'amount'=>$data->faceValue+reseller_comission($data->faceValue),
            'txid'=>$txid,
            'type'=>'International',
            'operator'=>$data->product->productName,
            'status'=>'completed',
            'cost'=>round($data->invoiceAmount,2),
            'transaction_id_company'=>$data->transactionId,
            'country_code'=>$country_code,
            'discount'=>$discount,
            'reseller_com'=>$reseller_profit,
            'admin_com'=>$admin_profit,
            'service'=>$service,
            'deliveredAmount'=>floor($data->topupDetail->localCurrencyAmount),
            'deliveredAmountCurrencyCode'=>$data->topupDetail->destinationCurrency,
            'company_name'=>'Ppn'

        ]);
        return $recharge;
    }

    public function create_pin($data,$txid)
    {
        $discount = $data->faceValue - $data->invoiceAmount;
        $reseller_profit = reseller_profit_white_calling($data->faceValue);
        //$reseller_com =round(($discount*.60),2);
        $admin_profit = $discount-$reseller_profit;

       $recharge = RechargeHistory::create([
            'reseller_id'=>a::user()->id,
            'amount'=>$data->faceValue,
            'txid'=>$txid,
            'type'=>'White Calling',
            'operator'=>$data->product->productName,
            'status'=>'completed',
            'cost'=>round($data->invoiceAmount,2),
            'transaction_id_company'=>$data->transactionId,
            'discount'=>$discount,
            'reseller_com'=>$reseller_profit,
            'admin_com'=>$admin_profit,
            'deliveredAmount'=>floor($data->pins[0]->deliveredAmount),
            'deliveredAmountCurrencyCode'=>$data->pins[0]->deliveredCurrencyCode,
            'company_name'=>'International2',
            'pin_number'=>$data->pins[0]->pinNumber,
            'control_number'=>$data->pins[0]->controlNumber

        ]);
        return $recharge;
    }

    public function update_balance($recharge_amount,$cost)
    {
        $balance_info = Balance::where('type','ppn')->first();
        $current_balance = $balance_info->balance-$cost;
        $discount = $recharge_amount-$cost;
        $total_cost_reseller = $cost+($discount/2);
        Balance::where('type','ppn')->update(['balance'=>$current_balance]);

    }


    public function recharge(Request $request)
    {

        //file_put_contents('test.txt',$test);
        if(!CheckRechargeAvail::check($request->amount))
        {
            return ['status'=>false,'message'=>'Insufficient wallet & Limit. Please contact with admin'];
        }
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
    // $tmp_data = json_decode($tmp_data);
     //$data = ['status'=>true,'payload'=>$tmp_data];
     //file_put_contents('test.txt',$tmp_data->responseCode);

        if($data['status']){
           // file_put_contents('test.txt',$data['payload']);
          $recharge = $this->create_recharge($data['payload']->payLoad,$number,$txid,$country_code,$request->service_charge);
          UpdateWallet::update($recharge);
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

    public function calling_card_index()
    {
        if(a::user()->role == 'user'){
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type','White Calling')->latest()->take(10)->get();
        }else{
            $data = RechargeHistory::where('type','White Calling')->join('users','users.id','=','recharge_histories.reseller_id')
            ->select('recharge_histories.*','users.nationality')
            ->latest()
            ->take(10)
            ->get();


        }

        return view('front.white-calling-ppn',compact('data'));
    }

    public function get_white_calling_table()
    {
        if(a::user()->role == 'user'){
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type','White Calling')->where('company_name','Ppn')->latest()->take(10)->get();
        }else{
            $data = RechargeHistory::where('type','White Calling')->where('company_name','Ppn')->join('users','users.id','=','recharge_histories.reseller_id')
            ->select('recharge_histories.*','users.nationality')
            ->latest()
            ->take(10)
            ->get();


        }
        return $data;

    }

    public function pin(Request $request)
    {
        if(!CheckRechargeAvail::check($request->amount))
        {
            return ['status'=>false,'message'=>'Insufficient wallet & Limit. Please contact with admin'];
        }
        $skuId = $request->skuId;
        $transaction =  new GenerateTransactionId(a::user()->id,32);
        $txid = $transaction->transaction_id();
       $data = $this->ppn->pin($skuId,$txid);
        // $tmp_text = '{
        //     "responseCode": "000",
        //     "responseMessage": null,
        //     "payLoad": {
        //         "transactionId": 129064031,
        //         "transactionDate": "12/12/2021 07:05",
        //         "invoiceAmount": 1.62,
        //         "faceValue": 2,
        //         "discount": 0,
        //         "fee": 0,
        //         "product": {
        //             "skuId": 3576,
        //             "productName": "White Calling PINS - Italy",
        //             "faceValue": 2,
        //             "instructions": ""
        //         },
        //         "topupDetail": null,
        //         "pins": [
        //             {
        //                 "pinNumber": "822 0276 652",
        //                 "controlNumber": "10728765",
        //                 "deliveredAmount": 2,
        //                 "deliveredCurrencyCode": "EUR"
        //             }
        //         ],
        //         "giftCardDetail": null,
        //         "simInfo": null,
        //         "billPaymentDetail": null
        //     }
        // }';
        // $data = json_decode($tmp_text);
        // $data = ['status'=>true,'payload'=>$data];
        if($data['status']){


           $recharge = $this->create_pin($data['payload']->payLoad,$txid);
           UpdateWallet::update($recharge);
           $this->update_balance($data['payload']->payLoad->faceValue,$data['payload']->payLoad->invoiceAmount);
         return ['status'=>true,'message'=>'Recharge Successfull','pin_number'=>$data['payload']->payLoad->pins[0]->pinNumber,'control_number'=>$data['payload']->payLoad->pins[0]->controlNumber];
         }
     else
     {
         $data = $data['payload'];
         return ['status'=>false,'message'=>$data->message];
     }
    }


}
