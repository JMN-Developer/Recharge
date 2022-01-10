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
       //$data = $this->dtone->recharge($skuId,$txid,$number);
    //     $tmp_data = '{
    //         "benefits":[
    //            {
    //               "additional_information":null,
    //               "amount":{
    //                  "base":100,
    //                  "promotion_bonus":0,
    //                  "total_excluding_tax":100
    //               },
    //               "type":"CREDITS",
    //               "unit":"BDT",
    //               "unit_type":"CURRENCY"
    //            }
    //         ],
    //         "confirmation_expiration_date":"2021-12-23T15:58:53.049511000Z",
    //         "creation_date":"2021-12-23T14:58:53.049511000Z",
    //         "credit_party_identifier":{
    //            "mobile_number":"+8801845318609"
    //         },
    //         "external_id":"23122021145852002312",
    //         "id":2237551912,
    //         "prices":{
    //            "retail":{
    //               "amount":1.5,
    //               "fee":0,
    //               "unit":"EUR",
    //               "unit_type":"CURRENCY"
    //            },
    //            "wholesale":{
    //               "amount":1.2,
    //               "fee":0,
    //               "unit":"EUR",
    //               "unit_type":"CURRENCY"
    //            }
    //         },
    //         "product":{
    //            "description":"",
    //            "id":3425,
    //            "name":"100 BDT",
    //            "operator":{
    //               "country":{
    //                  "iso_code":"BGD",
    //                  "name":"Bangladesh",
    //                  "regions":null
    //               },
    //               "id":1446,
    //               "name":"Robi Bangladesh",
    //               "regions":null
    //            },
    //            "regions":null,
    //            "service":{
    //               "id":1,
    //               "name":"Mobile"
    //            },
    //            "tags":[
    //               "AIRTIME"
    //            ],
    //            "type":"FIXED_VALUE_RECHARGE"
    //         },
    //         "promotions":null,
    //         "rates":{
    //            "base":83.3333333333333,
    //            "retail":66.6666666666667,
    //            "wholesale":83.3333333333333
    //         },
    //         "status":{
    //            "class":{
    //               "id":1,
    //               "message":"CREATED"
    //            },
    //            "id":10000,
    //            "message":"CREATED"
    //         }
    //      }';

    // $tmp_data = json_decode($tmp_data);
     //$data = ['status'=>true,'payload'=>$tmp_data];
    //  //file_put_contents('test.txt',$tmp_data->responseCode);

        if($data['status']){

            if($data['payload']->status->class->message=='REJECTED')
            {
                return ['status'=>false,'message'=>$data['payload']->status->message];
            }

            if($data['payload']->status->class->message=='CANCELLED')
            {
                return ['status'=>false,'message'=>$data['payload']->status->message];
            }

            if($data['payload']->status->class->message=='DECLINED')
            {
                return ['status'=>false,'message'=>$data['payload']->status->message];
            }


           // file_put_contents('test.txt',$data['payload']);
        $recharge = $this->create_recharge($data['payload'],$number,$txid,$country_code,$request->service_charge);
         UpdateWallet::update($recharge);
          $this->update_balance($data['payload']->prices->retail->amount,$data['payload']->prices->wholesale->amount);
        return ['status'=>true,'message'=>'Recharge Successfull'];
        }
    else
    {
        $data = $data['payload'];
        return ['status'=>false,'message'=>$data->message];
    }


    }
}
