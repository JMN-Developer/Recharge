<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SoapClient;
use App\Services\BangladeshiRecharge;
use App\Services\GenerateTransactionId;
use App\Services\UpdateWallet;
use App\Models\RechargeHistory;

use Auth;
class BangladeshRechargeController extends Controller
{
    //
    private $bangladeshi_recharge;
    public function __construct()
    {
        $this->bangladeshi_recharge = new BangladeshiRecharge();
    }

    public function create_recharge($number,$amount,$updated_amount,$txid,$operator_name,$transaction_id_company,$service)
    {
        // $discount =$data->prices->retail->amount - $data->prices->wholesale->amount;
        // $total_commission = reseller_comission($data->prices->retail->amount);
        // $reseller_profit = reseller_profit($total_commission);
        // $admin_profit = $total_commission-$reseller_profit;
        $cost = 0;
        $recharge = RechargeHistory::create([
            'reseller_id'=>a::user()->id,
            'number'=>$number,
            'amount'=>$amount,
            'txid'=>$txid,
            'type'=>'International',
            'operator'=>$operator_name,
            'status'=>'completed',
            'cost'=>$cost,
            'transaction_id_company'=>$transaction_id_company,
            'service'=>$service,
            'reseller_com'=>$service,
            'admin_com'=>$admin_profit,
            'deliveredAmount'=>$updated_amount,
            'deliveredAmountCurrencyCode'=>'BDT',
            'company_name'=>'International5'

        ]);
        return $recharge;
    }

    public function recharge(Request $request)
    {
       $amount = $request->amount;
       $change = [' ','+'];
        $msisdn = str_replace($change,'',$request->number);
        $operator_id = $request->operator_id;
        $guid =  new GenerateTransactionId(Auth::user()->id,13);
        $guid =  $guid->transaction_id();
        //file_put_contents('test.txt',$request->operator_id);
       $create_recharge = $this->bangladeshi_recharge->CreateRecharge($guid,$operator_id,$msisdn,$amount);
       $init_recharge =  $this->bangladeshi_recharge->InitRecharge($guid,$create_recharge['data']->vr_guid);
       if($init_recharge['data']->recharge_status == 200)
       {
        $recharge = $this->create_recharge($msisdn,$request->updated_amount,$amount,$guid,$request->operator_name,$create_recharge['data']->vr_guid,$request->service_charge);
        UpdateWallet::update($recharge);
         $this->update_balance($data['payload']->prices->retail->amount,$data['payload']->prices->wholesale->amount);
        return ['status'=>true,'message'=>'Recharge Successfull'];
       }
       else
       {
        return ['status'=>false,'message'=>$data['message']];
       }
    }

    public function bangladeshi_exchange_rate(Request $request){
       $rate = 1.04;
       $unit_rate = 100/$rate;
        $value = $request->value;
        $updated_value = $value*$unit_rate;
        echo floor($updated_value);

    }

    public function index()
    {
        return view('front.recharge-bangladesh');
      //dd($this->bangladeshi_recharge->balanceInfo());
     //echo json_encode($this->bangladeshi_recharge->getOperatorLimit('3'));
      //dd( $this->bangladeshi_recharge->operatorInfo('8801845318609'));


    }
    public function mobile_number_details(Request $request)
    {
       // $msisdn = $request->number;
        $change = [' ','+'];
        $msisdn = str_replace($change,'',$request->number);
        $operator_details =  $this->bangladeshi_recharge->operatorInfo($msisdn);
        if($operator_details['soap_exception_occured']==false)
        {
            $rate = 1.04;
            $unit_rate = $rate/100;
            $data =  $this->bangladeshi_recharge->offer_details($operator_details['data']->operator_id);
            foreach($data as $d)
            {
                $d->update_amount =  round($d->amount*$unit_rate,4);
            }

            return ['status'=>true,'offer_data'=>$data,'operator_id'=>$operator_details['data']->operator_id,'operator_name'=>$operator_details['data']->operator_name];
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
