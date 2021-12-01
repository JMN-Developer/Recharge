<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use Illuminate\Http\Request;
use App\Models\RechargeHistory;
use App\Models\User;
use App\Services\ReloadlyProvider;
use Auth as a;
use App\Services\GenerateTransactionId;


class ReloadlyController extends Controller
{
    //

    protected $reloadly;
    public function __construct(ReloadlyProvider $reloadly)
    {
        $this->reloadly = $reloadly;
    }

    public function index()
    {


        if(a::user()->role == 'user'){
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type','International')->where('company_name','Reloadly')->latest()->take(10)->get();
        }else{
            $data = RechargeHistory::where('type','International')->where('company_name','Reloadly')->join('users','users.id','=','recharge_histories.reseller_id')
            ->select('recharge_histories.*','users.nationality')
            ->latest()
            ->take(10)
            ->get();


        }

        return view('front.recharge-reloadly',compact('data'));
    }

    public function mobile_number_details(Request $request)
    {
        $change = [' ','+'];
        $number = str_replace($change,'',$request->number);
        $countryIso = $request->countryIso;
        //$number = $request->number;
        $data = $this->reloadly->operator_details($number,$countryIso);
        if($data['status']){
            $data = (array)$data['payload'];
            return ['status'=>true,'data'=>$data];
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

    public function create_recharge($data)
    {
        $reseller_com = $data->discount/2;
        $admin_com = $data->discount-$reseller_com;
        RechargeHistory::create([
            'reseller_id'=>a::user()->id,
            'number'=>$data->recipientPhone,
            'amount'=>$data->requestedAmount,
            'txid'=>$data->customIdentifier,
            'type'=>'International',
            'operator'=>$data->operatorName,
            'status'=>'completed',
            'cost'=>$data->requestedAmount-$data->discount,
            'transaction_id_reloadly'=>$data->transactionId,
            'country_code'=>$data->countryCode,
            'discount'=>$data->discount,
            'reseller_com'=>$reseller_com,
            'admin_com'=>$admin_com,
            'deliveredAmount'=>$data->deliveredAmount,
            'deliveredAmountCurrencyCode'=>$data->deliveredAmountCurrencyCode,
            'company_name'=>'Reloadly'

        ]);
    }

    public function update_balance($balance,$requested_amount)
    {
        Balance::where('type','reloadly')->update(['balance'=>$balance]);
        if(a::user()->role != 'admin')
        {
            $existing_wallet = User::where('id',a::user()->id)->first()->wallet;
            $new_wallet = $existing_wallet-$requested_amount;
            User::where('id',a::user()->id)->update(['wallet'=>$new_wallet]);

        }
    }

    public function reloadly_recharge(Request $request)
    {
        $change = [' ','+'];
        $number = str_replace($change,'',$request->number);
        $amount = $request->amount;
        $countryCode = $request->countryCode;
        $operatorId = $request->operatorId;
        $transaction =  new GenerateTransactionId(a::user()->id,10);
        $txid = $transaction->transaction_id();
        $data = $this->reloadly->recharge($operatorId,$amount,$countryCode,$number,$txid);

        if($data['status']){
          $this->create_recharge($data['payload']);
          $this->update_balance($data['payload']->balanceInfo->newBalance,$data['payload']->requestedAmount);
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
