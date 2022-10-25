<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth as a;
use Carbon\Carbon;
use App\Models\Pin;
use App\Models\DomesticProfit;
use App\Models\Balance;
use App\Models\User;
use App\Services\GenerateTransactionId;
use DB;
use App\Models\DomesticProduct;
use App\Services\UpdateWallet;
use App\Models\RechargeHistory;
use App\Services\CheckRechargeAvail;
use Illuminate\Support\Facades\Log;

class PinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(a::user()->role == 'user'){
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type','pin')->latest()->take(10)->get();
        }else{
            $data = RechargeHistory::where('type','pin')->latest()->take(10)->get();
        }
        return view('front.pin-domestic',compact('data'));
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sku_amount = explode(',',$request->amount);
        if(!CheckRechargeAvail::check( $sku_amount[1],'Domestic'))
        {
            return  Redirect()->back()->with('error','Insufficient wallet & Limit. Please contact with admin');

        }
        $change = [' ','Mobile','mobile'];
        $operator = str_replace($change,'',$request->operator);

        //file_put_contents('test.txt',$sku_amount[1]);
       // return;

        $amount = $sku_amount['1']*100;
        $commission = DB::table('domestic_pins')->where('ean', $sku_amount['0'])->first()->commission;

        $amount = str_replace('.','',$amount);


        if (CheckRechargeAvail::check($sku_amount[1],'Domestic')) {
            $transaction =  new GenerateTransactionId(a::user()->id,40);
              $txid = $transaction->transaction_id();


        $xml = '<?xml version="1.0" ?><REQUEST MODE="DIRECT" STORERECEIPT="1" TYPE="SALE">
        <USERNAME>UPLIVE_AMICIBIGIOTTERIA</USERNAME>
        <TXID>'.$txid.'</TXID>
        <RECEIPT><LANGUAGE>ITA</LANGUAGE><CHARSPERLINE>40</CHARSPERLINE><TYPE>FULLTEXT</TYPE></RECEIPT>
        <AMOUNT>'.$amount.'0</AMOUNT>
        <TERMINALID RETAILERACC="PNTRCG" STOREID="3D001">IT028215</TERMINALID>
        <CURRENCY></CURRENCY>
        <CARD><EAN>'.$sku_amount['0'].'</EAN></CARD>
        <LOCALDATETIME>'.Carbon::now('Europe/Berlin').'</LOCALDATETIME>
        <CAB>3D001</CAB>
        <PASSWORD>db2ec37cc93a3525</PASSWORD>
        </REQUEST>';

        // $req = $client->request(["Content-Type" => "application/xml"])
        //                ->post('https://precision.epayworldwide.com/up-interface', [$xml,'verify' => false]);

        $client = new \GuzzleHttp\Client();
        $recharge_request = $client->post('https://precision.epayworldwide.com/up-interface',[
            'headers' => [
            'api_key'     => 'Etmo8i5V9q862PHn5dNJSb',
            'content_type' => 'application/xml'
            ],
            'verify' => false,
            'body' => $xml
        ]);

        $body = $recharge_request->getBody();
        $xml = simplexml_load_string($body);



        $pin = $xml->PINCREDENTIALS;

        if($xml->RESULT == 0){

            $balancequery = Balance::where('type','domestic')->first();

            $prof = DomesticProfit::where('ean',$sku_amount['0'])->first();

            $noted = json_encode($xml->RECEIPT->LINE);


            $c = ['1','2','3','4','5','6','7','8','9','0','"',',',':','{','}','/','\\'];

            $note = str_replace($c,'',$noted);




            $balance = DB::table('balances')->where('type','domestic')->update([
                'balance' => round(($xml->LIMIT)/100,2),

            ]);


            if(a::user()->role != 'admin'){
                if(auth()->user()->role =='reseller'){
                    $cost = $sku_amount['1'] - $prof->commission;
                    $parent_commission = parent_profit_pin($prof->commission);
                    $admin_commission = $prof->commission - $parent_commission;
                    $reseller_commission = reseller_profit_pin($parent_commission); 
                    $sub_profit = $parent_commission - $reseller_commission; 
                }
                else{
                $reseller_commission = reseller_profit_pin($commission);
                $admin_commission = $commission -  $reseller_commission;
                $cost = $sku_amount['1']-$commission;
                $sub_profit = 0;
                }


            }else{
                $reseller_commission = 0;
                $admin_commission = 0;
                $cost = $sku_amount['1']-$commission;
                $sub_profit = 0;
            }

            $product = db::table('domestic_pins')->where('ean',$sku_amount['0'])->first();

            $log_data = 'PIN = '.$pin->PIN.' Amount = '.$sku_amount['1'].' R-Com = '.$reseller_commission.' A-Com = '.$admin_commission.' TXID = '.$txid;
            Log::channel('rechargelog')->info($log_data);
        $create = new RechargeHistory;

        $create->reseller_id = a::user()->id;

        $create->amount = $sku_amount['1'];

        $create->txid = $txid;

        $create->type = 'pin';

        $create->cost = $cost;
        $create->company_name = 'Epay';

        $create->pin_number = $pin->PIN;

        $create->pin_serial = $pin->SERIAL;

        $create->pin_validity = $pin->VALIDTO;

        $create->operator = $operator;

        $create->status = 'success';

        $create->reseller_com = $reseller_commission;

        $create->admin_com = $admin_commission;

        $create->pin_note = $note;

        $create->pin_product = $product->product;
        $create->sub_profit = $sub_profit;

        $create->save();

        UpdateWallet::update($create);

        return ['status'=>true,'message'=>'Your Pin Purchase Has Been Sucessfull! Here is your pin '.$pin->PIN];
        //return  Redirect('recharge/pin/')->with('status',);


        }else{
            return ['status'=>false,'message'=>'Error occured Please try again!'];
            return  Redirect()->back()->with('error','Error occured Please try again!');
        }


        // $data = json_encode($bod,true);

        }else{
            return ['status'=>false,'message'=>'Insufficient Balance'];
            // return  Redirect()->back()->with('error','Insufficient Balance');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoices()
    {
        if(a::user()->role == 'admin'){
            $data = Pin::latest()->get();
            $cost = $data->sum('amount');
            // $profit = $data->sum('admin_com');
        }else{
            $data = Pin::where('reseller_id', a::user()->id)->latest()->get();
            $cost = $data->sum('cost');
            // $profit = $data->sum('reseller_com');
        }
        return view('front.print-all-invoice_pin',compact('data','cost'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $data = Pin::where('id', $id)->first();

        return view('front.recharge_invoice',compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

}
