<?php

namespace App\Http\Controllers;

use App\Exports\RechargeExport;
use App\Models\Balance;
use App\Models\DomesticProfit;
use App\Models\Operator;
use App\Models\Pin;
use App\Models\Recharge;
use App\Models\RechargeHistory;
use App\Models\User;
use App\Services\CheckRechargeAvail;
use App\Services\GenerateTransactionId;
use App\Services\SecretProvider;
use App\Services\UpdateWallet;
use Auth as a;
use Carbon\Carbon;
use DataTables;
use DB;

// edit by shuvo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Maatwebsite\Excel\Facades\Excel;

class RechargeController extends Controller
{
    // properties
    protected $factory;
    protected $dingconnect;
    protected $epay;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function exportToExcel(Request $request)
    {

        return Excel::download(new RechargeExport($request->start_date, $request->end_date, $request->type), 'invoices.xlsx');
        // return Excel::download(new RechargeExport(), 'data.xlsx');

    }

    public function data_test()
    {
        return SecretProvider::get_secret('Dingconnect');
        // $date = date("dmYHis");
        // $time = date('His');
        // echo $date;
    }

    public function __construct()
    {
        $this->factory = (new Factory())->withServiceAccount(__DIR__ . '/FirebaseKey.json');
        $this->dingconnect = SecretProvider::get_secret('Dingconnect');
        $this->epay = SecretProvider::get_secret('epay');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //  INTERNATIONAL RECHARGE
    //   work here shovon
    public function RechargeInt($value = '')
    {
        $stage = 'initial';
        if (a::user()->role != 'admin') {
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type', 'International')->take(10)->get();
        } else {
            $data = RechargeHistory::where('type', 'International')->join('users', 'users.id', '=', 'recharge_histories.reseller_id')
                ->select('recharge_histories.*', 'users.nationality')

                ->take(10)
                ->get();
        }

        return view('front.recharge-international', compact('stage', 'data'));
    }

//  shovon work here
// create model researchtransaction

    public function RechargeDom($value = '')
    {
        if (a::user()->role != 'admin') {
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type', 'Domestic')->take(10)->latest()->get();
        } else {
            $data = RechargeHistory::where('type', 'Domestic')->join('users', 'users.id', '=', 'recharge_histories.reseller_id')
                ->select('recharge_histories.*', 'users.nationality')
                ->take(10)
                ->latest()
                ->get();
        }
        return view('front.recharge-domestic', compact('data'));
    }

    public function RechargeGiftCard($value = '')
    {
        return view('front.recharge-gift-card');
    }

    public function RechargeCallingCard($value = '')
    {
        return view('front.recharge-calling-card');
    }

    public function PrintInvoice($value = '')
    {
        return view('front.print-all-invoice');
    }

    public function index(Request $request)
    {
        // $factory = (new Factory)->withServiceAccount(__DIR__.'/FirebaseKey.json');
        $text = $request->text;
        $database = $this->factory->createDatabase();

        $database->getReference('newspaper-c6671')
            ->set([
                'body' => $text,
            ]);

        return response()->json(['success' => 'true', 'Code' => $text]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
    public function response(Request $request)
    {
        if ($request->status == 1) {
            $update = Recharge::where('recharge_id', $request->id)->update([
                'status' => "success",
            ]);
        } else {
            $update = Recharge::where('recharge_id', $request->id)->update([
                'status' => "failed",
            ]);
        }

        return $update;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $data = RechargeHistory::where('id', $id)->first();

        return view('front.recharge_invoice', compact('data'));
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
    public function operator(Request $request)
    {
        $operator = Operator::where('operator', $request->operator)->first();
        if ($operator != null) {
            $new = Operator::where('operator', $request->operator)->update([
                'operator' => $request->operator,
                'token' => $request->token,
            ]);

            return $operator;
        } else {
            $new = Operator::create([
                'operator' => $request->operator,
                'token' => $request->token,
            ]);
            return $new;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function check_operator(Request $request)
    {
        $change = [' ', '+'];
        $number = str_replace($change, '', $request->number);

        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://api.dingconnect.com/api/V1/GetProviders?accountNumber=' . $number, ['headers' => [
            'api_key' => $this->dingconnect,
        ], 'verify' => false]);
        $operator_response = $operator_request->getBody();
        // $operator_response = '{
        //     "ResultCode":1,
        //     "ErrorCodes":[

        //     ],
        //     "Items":[
        //        {
        //           "ProviderCode":"6DBD",
        //           "CountryIso":"BD",
        //           "Name":"Robi Bangladesh Data",
        //           "ShortName":null,
        //           "ValidationRegex":"^8800?([0-9]{10})$",
        //           "CustomerCareNumber":"+88029897806",
        //           "RegionCodes":[
        //              "BD"
        //           ],
        //           "PaymentTypes":[
        //              "Prepaid"
        //           ],
        //           "LogoUrl":"https://imagerepo.ding.com/logo/6D/BD.png"
        //        },
        //        {
        //           "ProviderCode":"RLBD",
        //           "CountryIso":"BD",
        //           "Name":"Robi Bangladesh",
        //           "ShortName":null,
        //           "ValidationRegex":"^8800?([0-9]{10})$",
        //           "CustomerCareNumber":null,
        //           "RegionCodes":[
        //              "BD"
        //           ],
        //           "PaymentTypes":[
        //              "Prepaid"
        //           ],
        //           "LogoUrl":"https://imagerepo.ding.com/logo/RL/BD.png"
        //        }
        //     ]
        //  }';
        $data = json_decode($operator_response, true);
        // dd($data);
        $count = count($data['Items']);
        if ($count != 0) {
            $operators = $data['Items'];
            // dd($operators['0']);
            $datas = $request->all();
            $datas['number'] = $number;
            $stage = 'check_number';
            if (a::user()->role != 'admin') {
                $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type', 'International')->take(10)->latest()->get();
            } else {
                $data = RechargeHistory::where('type', 'International')->join('users', 'users.id', '=', 'recharge_histories.reseller_id')
                    ->get('recharge_histories.*', 'users.nationality')
                    ->latest()
                    ->take(10)
                    ->get();
            }
            $count = '1';
            return $pass = $this->get_product($request, $operators['0']['Name'], $operators['0']['ProviderCode'], $number, $operators['0']['LogoUrl']);
        } else {
            $error = 'Invalid Phone Number';
            // return ['status'=>false,'message'=>$error]
            return redirect('/recharge/recharge-int')->with('error', $error);
        }

        // return view('front.recharge-international',compact('operators','datas','stage','data','count'));
    }

    public function change_operator($numbers, $rg)
    {
        $change = [' ', '+'];
        $number = str_replace($change, '', $numbers);
        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://api.dingconnect.com/api/V1/GetProviders?regionCodes=' . $rg, ['headers' => [
            'api_key' => 'G4ymoFlN97B6PhZgK1yzuY',
        ], 'verify' => false]);
        $operator_response = $operator_request->getBody();
        $data = json_decode($operator_response, true);
        $operators = $data['Items'];
        // dd($operators['0']);
        // $datas = $request->all();
        $datas['number'] = $number;
        $stage = 'check_number';
        if (a::user()->role != 'admin') {
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type', 'International')->take(10)->latest()->get();
        } else {
            $data = RechargeHistory::where('type', 'International')->join('users', 'users.id', '=', 'recharge_histories.reseller_id')
                ->get('recharge_histories.*', 'users.nationality')
                ->latest()
                ->take(10)
                ->get();
        }
        $count = '1';

        return view('front.recharge-international', compact('operators', 'stage', 'data', 'count', 'datas'));
    }

    // edit by shuvo
    public function fcmSend(Request $request)
    {
        $user = User::where('id', $request->authid)->first();

        $recharge_id = Str::random(10);

        $recharge = Recharge::create([
            'recharge_id' => $recharge_id,
            'number' => $request->number,
            'amount' => $request->amount,
            'user_id' => $request->authid,
            'operator' => 'robi',
            'status' => 'pending',
        ]);

        $messaging = $this->factory->createMessaging();

        // $topic = 'news';

        $token = "eS94mTBKSfaZZHKhUDFyeo:APA91bF_MtvOHYcL3rD0AqjZZPgDo1ZRPD4YwEo6YQw9Gtej8dB9Xtc2yBkjQnFT7b3B68LtxviWEFo8oSm-6h69TWq45JAP5vDgaiXRvRZno0u3VXUOuiOUbx8QVxbxQwJZ9vhgcD2V";

        $title = $recharge_id;
        $body = "*999*" . $request->number . "*" . $request->amount . "*1985#";

        //         $title = $request->title;
        //         $body = $request->body;

        $notification = Notification::create($title, $body);

        $data = [
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'action' => 'new_notification',
            'notification_icon' => null,
            'notification_status' => 'active',
            'title' => $title,
            'body' => $body,
            'icon' => 'stock_ticker_update',
            'color' => '#f45342',
            'sound' => 'default',
        ];

        $config = AndroidConfig::fromArray([
            'ttl' => '3600s',
            'priority' => 'high',
            'notification' => [
                'title' => $title,
                'body' => $body,
                'icon' => 'stock_ticker_update',
                'color' => '#f45342',
                'sound' => 'default',
            ],
        ]);

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification)
            ->withData($data)
            ->withAndroidConfig($config);

        //         try {
        //             $messaging->validate($message);
        //             // or
        //             return $messaging->send($message, $validateOnly = true);
        //         } catch (InvalidMessage $e) {
        //             print_r($e->errors());
        //         }
        $messaging->send($message);

        a::login($user);

        return redirect('/');
    }

    public function get_product(Request $request, $operator = '', $code = '', $number = '', $logo = '')
    {
        $datas = $request->all();
        // dd($number);
        $datas['operator'] = $operator;

        $change = [' ', '+'];
        // $number = str_replace($change,'',$request->number);

        //  dd($number);
        $client = new \GuzzleHttp\Client();
        $product_request = $client->get('https://api.dingconnect.com/api/V1/GetProducts?&providerCodes=' . $code, ['headers' => [
            'api_key' => 'G4ymoFlN97B6PhZgK1yzuY',
        ], 'verify' => false]);
        $product_responses = $product_request->getBody();

        $prod = json_decode($product_responses, true);

        // dd($prod);

        $rg = $prod['Items']['0']['RegionCode'];

        $prods = $prod['Items'];

        $count = count($prods);

        // dd($prods);
        $stage = 'get_product';

        if (a::user()->role != 'admin') {
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type', 'International')->take(10)->get();
        } else {
            $data = RechargeHistory::where('type', 'International')->join('users', 'users.id', '=', 'recharge_histories.reseller_id')
                ->get('recharge_histories.*', 'users.nationality')

                ->take(10)
                ->get();
        }

        return view('front.recharge-international', compact('datas', 'prods', 'count', 'stage', 'data', 'rg', 'logo'));
    }

    public function get_changed_product(Request $request)
    {
        $datas = $request->all();
        // dd($number);
        $datas['operator'] = $request->operator;

        $change = [' ', '+'];
        // $number = str_replace($change,'',$request->number);

        //  dd($number);
        $client = new \GuzzleHttp\Client(['defaults' => ['exceptions' => false]]);
        $product_request = $client->get('https://api.dingconnect.com/api/V1/GetProducts?&providerCodes=' . $request->operator, ['headers' => [
            'api_key' => $this->dingconnect,
        ], 'verify' => false]);
        $product_responses = $product_request->getBody();

        $prod = json_decode($product_responses, true);

        // dd($prod);

        $rg = $prod['Items']['0']['RegionCode'];

        $prods = $prod['Items'];

        $count = count($prods);

        // dd($prods);
        $stage = 'get_product';

        if (a::user()->role != 'admin') {
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type', 'International')->take(10)->get();
        } else {
            $data = RechargeHistory::where('type', 'International')->join('users', 'users.id', '=', 'recharge_histories.reseller_id')
                ->get('recharge_histories.*', 'users.nationality')

                ->take(10)
                ->get();
        }

        return view('front.recharge-international', compact('datas', 'prods', 'count', 'stage', 'data', 'rg'));
    }

    public function check_daily_duplicate(Request $request)
    {
        $number = $request->number;
        $change = [' ', '+'];
        $number = str_replace($change, '', $number);

        $avail = RechargeHistory::where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())->where('number', $number)->first();

        if ($avail) {
            return '1';
        } else {
            return '0';
        }
    }

    public function recharge(Request $request)
    {
        $change = [' ', '+'];
        $number = str_replace($change, '', $request->number);

        //  dd($number);
        $transaction = new GenerateTransactionId(a::user()->id, 10);
        $txid = $transaction->transaction_id();

        //$txid = mt_rand(1000000000, 9999999999);

        $datas = $request->all();
        // dd($datas);

        $received = $request->received_amount;
        $sku_amount = explode(',', $datas['amount']);

        // dd($sku_amount);

        if (count($sku_amount) > 1) {
            $SkuCode = $sku_amount['0'];
            $SendValue = $sku_amount['1'];
        } else {
            $SkuCode = $datas['Sku_Code'];
            $SendValue = $datas['amount'];
        }

        if (CheckRechargeAvail::check($SendValue, 'International')) {
            $client = new \GuzzleHttp\Client(['http_errors' => false]);
            $recharge_request = $client->post('https://api.dingconnect.com/api/V1/SendTransfer', [
                'headers' => [
                    'api_key' => $this->dingconnect,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'json' => [
                    'SkuCode' => $SkuCode,
                    'SendValue' => $SendValue,
                    'AccountNumber' => $number,
                    'DistributorRef' => $txid,
                    'ValidateOnly' => false,
                ],
            ]);

            $product_responses = $recharge_request->getBody();
            $prod = json_decode($product_responses, true);
            $count = count($prod['ErrorCodes']);

            if ($count == 0) {
                $refcost = $SendValue + reseller_comission($SendValue);

                $client = new \GuzzleHttp\Client();
                $product_request = $client->get('https://api.dingconnect.com/api/V1/GetBalance', ['headers' => [
                    'api_key' => $this->dingconnect,
                ], 'verify' => false]);
                $product_responses = $product_request->getBody();

                $prod = json_decode($product_responses, true);

                $bal = $prod['Balance'];

                $balancequery = Balance::where('type', 'ding')->first();

                $balance = DB::table('balances')->where('type', 'ding')->update([
                    'balance' => $bal,
                ]);

                $total_commission = reseller_comission($SendValue);
                $reseller_profit = reseller_profit($total_commission);
                $admin_profit = $total_commission - $reseller_profit;

                $create = new RechargeHistory();
                $create->reseller_id = a::user()->id;
                $create->number = $request->number;
                $create->amount = $refcost;
                $create->reseller_com = $reseller_profit;
                $create->admin_com = $admin_profit;
                $create->txid = $txid;
                $create->operator = $request->operator;
                $create->type = 'International';
                $create->company_name = 'International1';
                $create->status = 'completed';
                $create->cost = $SendValue;
                $create->service = $request->service_charge;
                $create->save();
                UpdateWallet::update($create);
                return ['status' => true, 'message' => 'Recharge Successful!'];
            //return redirect('/recharge/recharge-int')->with('status','Recharge Successful!');
            } else {
                $error = $prod['ErrorCodes']['0']['Code'];
                return ['status' => false, 'message' => $error];
                //return redirect('/recharge/recharge-int')->with('error',$error);
            }
        } else {
            return ['status' => false, 'message' => 'Insufficient Balance!'];
            //return redirect('/recharge/recharge-int')->with('error','Insufficient Balance!');
        }
    }

    public function estimate(Request $request)
    {
        $data = $request->all();

        $Sku = $data['SkuCode'];
        $batch = $data['BatchItemRef'];
        $send = (float) $data['SendValue'];

        $sent["SkuCode"] = $Sku;

        $sent["BatchItemRef"] = $batch;

        $sent["SendValue"] = $send;

        $sented = json_encode([$sent]);

        // dd($sent);

        $url = 'https://api.dingconnect.com/api/V1/EstimatePrices';

        $payloadArray = array(
            "data" => $sent,
        );

        $auth = false;

        // dd($sented);

        // $client = new \GuzzleHttp\Client(['http_errors' => false]);
        //     $recharge_request = $client->post('https://api.dingconnect.com/api/V1/EstimatePrices',[
        //     'headers' => [
        //     'api_key'     => 'G4ymoFlN97B6PhZgK1yzuY',
        //     'Content-Type' => 'application/json'
        //     ],
        //     'json' =>[$sented]

        // ]);

        // $product_responses = $recharge_request->getBody();

        // $prod = json_decode($product_responses,true);

        // return $prod;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.dingconnect.com/api/V1/EstimatePrices',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '[

        {

            "SendValue": ' . $send . ',

            "SkuCode": "' . $Sku . '",

            "BatchItemRef": "' . $batch . '"

        }

        ]',
            CURLOPT_HTTPHEADER => array(
                'api_key: G4ymoFlN97B6PhZgK1yzuY',
                'Content-Type: application/json',
                'Cookie: incap_ses_1133_1694192=AwHWAm/BhlzZuzgc6Dm5D7rbWWEAAAAAo9JpBSlTH4IrRXDnVPx7Fg==; nlbi_1694192=6JpIeiOts2y5IjJ2GYVdWQAAAAD/6ShAvkegY47YOpZi0MML; visid_incap_1694192=3NpwJbT5Rfi4Xx/8TMLYt0HPImEAAAAAQUIPAAAAAAA0uxNXMfBiKihGapkEfTkn',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // return $response;
        $prod = json_decode($response, true);

        return $prod;
    }

    public function check_domestic_repeat($number)
    {
        $recharge = RechargeHistory::where('number', $number)->first();
        if ($recharge) {
            $startTime = Carbon::parse($recharge->created_at);
            $endTime = Carbon::parse(Carbon::now()->toDateTimeString());
            $totalDuration = $endTime->diffInMinutes($startTime);
            if ($totalDuration > 1) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function load_recent_domestice_recharge()
    {
        if (a::user()->role != 'admin') {
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where('type', 'Domestic')->take(10)->latest()->get();
        } else {
            $data = RechargeHistory::where('type', 'Domestic')->join('users', 'users.id', '=', 'recharge_histories.reseller_id')
                ->get('recharge_histories.*', 'users.nationality')
                ->latest()
                ->take(10)
                ->get();
        }
        foreach ($data as $item) {
            if (a::user()->role != 'admin') {
                $item->profit = $item->reseller_com;
            } else {
                $item->profit = $item->admin_com;
            }
        }
        return $data;
    }

    public function domestic_recharge(Request $request)
    {
        $change = [' ', 'Mobile', 'mobile'];
        $operator = str_replace($change, '', $request->operator);
        // file_put_contents('test.txt',$request->amount);
        if (!check_recurrent_recharge($request->number)) {
            return ['status' => false, 'message' => 'You can not recharge with same number within 10 seconds!'];
        }
        $sku_amount = explode(',', $request->amount);

        if (!CheckRechargeAvail::check($sku_amount['1'], 'Domestic')) {
            return ['status' => false, 'message' => 'Insufficient wallet & Limit. Please contact with admin'];
        }

        $txid = mt_rand(1000000000, 9999999999);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <REQUEST MODE="RESERVE" STORERECEIPT="1" TYPE="SALE">
        <USERNAME>UPLIVE_AMICIBIGIOTTERIA</USERNAME>
        <PASSWORD>' . $this->epay . '</PASSWORD>
        <RECEIPT><LANGUAGE>ITA</LANGUAGE><CHARSPERLINE>32</CHARSPERLINE><TYPE>FULLTEXT</TYPE></RECEIPT>
        <CURRENCY>978</CURRENCY>
        <AMOUNT>' . $sku_amount['1'] . '000</AMOUNT>
        <TERMINALID RETAILERACC="PNTRCG" STOREID="3D001">IT028215</TERMINALID>
        <LOCALDATETIME>' . Carbon::now('Europe/Berlin') . '</LOCALDATETIME>
        <TXID>' . $txid . '</TXID>
        <CARD><EAN>' . $sku_amount['0'] . '</EAN></CARD>
        <PHONE>' . $request->number . '</PHONE><CAB>
        3D001</CAB></REQUEST>';

        // $req = $client->request(["Content-Type" => "application/xml"])
        //                ->post('https://precision.epayworldwide.com/up-interface', [$xml,'verify' => false]);

        $client = new \GuzzleHttp\Client();
        $recharge_request = $client->post('https://precision.epayworldwide.com/up-interface', [
            'headers' => [
                'api_key' => 'Etmo8i5V9q862PHn5dNJSb',
                'content_type' => 'application/xml',
            ],
            'verify' => false,
            'body' => $xml,
        ]);

        $body = $recharge_request->getBody();
        $xml = simplexml_load_string($body);

        // $data = json_encode($bod,true);

        if ($xml->RESULT == 0) {
            //$txid2 = mt_rand(1000000000, 9999999999);
            $transaction = new GenerateTransactionId(a::user()->id, 20);
            $txid2 = $transaction->transaction_id();
            $xml2 = '<?xml version="1.0" encoding="UTF-8"?>
                <REQUEST MODE="CAPTURE" STORERECEIPT="1" TYPE="SALE">
                    <USERNAME>UPLIVE_AMICIBIGIOTTERIA</USERNAME>
                    <PASSWORD>' . $this->epay . '</PASSWORD>
                    <RECEIPT>
                        <LANGUAGE>ITA</LANGUAGE>
                        <CHARSPERLINE>32</CHARSPERLINE>
                        <TYPE>FULLTEXT</TYPE>
                    </RECEIPT>
                    <CURRENCY>978</CURRENCY>
                    <AMOUNT>' . $sku_amount['1'] . '000</AMOUNT>
                    <TERMINALID RETAILERACC="PNTRCG" STOREID="3D001">IT028215</TERMINALID>
                    <LOCALDATETIME>' . Carbon::now('Europe/Berlin') . '</LOCALDATETIME>
                    <TXID>' . $txid2 . '</TXID>
                        <CARD>
                            <EAN>' . $sku_amount['0'] . '</EAN>
                        </CARD>
                        <PHONE>' . $request->number . '</PHONE>
                        <CAB>3D0013D001</CAB>
                    <TXID/>
                    <TXREF>' . $txid . '</TXREF>
                    <CAB/>
                </REQUEST>';

            $client = new \GuzzleHttp\Client();
            $recharge_request = $client->post('https://precision.epayworldwide.com/up-interface', [
                'headers' => [
                    'api_key' => 'Etmo8i5V9q862PHn5dNJSb',
                    'content_type' => 'application/xml',
                ],
                'verify' => false,
                'body' => $xml2,
            ]);

            $body2 = $recharge_request->getBody();
            $xml2 = simplexml_load_string($body2);

            $balancequery = Balance::where('type', 'domestic')->first();

            $prof = DomesticProfit::where('ean', $sku_amount['0'])->first();

            $balance = DB::table('balances')->where('type', 'domestic')->update([
                'balance' => round(($xml2->LIMIT) / 100, 2),
            ]);
            if ($xml2->RESULT == 0) {
                if (a::user()->role != 'admin') {
                    if (auth()->user()->parent->role == 'sub') {
                        $cost = $sku_amount['1'] - $prof->commission; // 9.74
                        $parent_commission = parent_profit_domestic($prof->commission); // .13
                        $admin_commission = $prof->commission - $parent_commission; // .13
                        $reseller_commission = reseller_profit_domestic($parent_commission); //.09
                        $sub_profit = $parent_commission - $reseller_commission; // 0.13-0.09 = 0.04
                    } else {
                        $cost = $sku_amount['1'] - $prof->commission; //10- .26 = 9.74
                        $reseller_commission = reseller_profit_domestic($prof->commission); // (.26) = .13
                        $admin_commission = $prof->commission - $reseller_commission; //.26-.13 = .13
                        $sub_profit = 0;
                    }
                } else {
                    $reseller_commission = 0;
                    $admin_commission = 0;
                    $cost = $sku_amount['1'] - $prof->commission;
                    $sub_profit = 0;
                }

                $log_data = 'Number = ' . $request->number . ' Amount = ' . $sku_amount['1'] . ' R-Com = ' . $reseller_commission . ' A-Com = ' . $admin_commission . ' TXID = ' . $xml2->TXID;
                Log::channel('rechargelog')->info($log_data);
                $create = new RechargeHistory();
                $create->reseller_id = a::user()->id;
                $create->number = $request->number;
                $create->amount = $sku_amount['1']; //10
                $create->operator = $operator;
                $create->reseller_com = $reseller_commission; //.13
                $create->admin_com = $admin_commission;
                $create->txid = $xml2->TXID;
                $create->type = 'Domestic';
                $create->status = 'completed';
                $create->cost = $cost;
                $create->company_name = 'Domestic1';
                $create->sub_profit = $sub_profit;
                $create->recharge_comission = a::user()->admin_recharge_commission;
                $create->save();
                UpdateWallet::update($create);
                return ['status' => true, 'message' => 'Your Recharge Has Been Sucessfull!'];
            //return  Redirect()->back()->with('status','Your Recharge Has Been Sucessfull!');
            } else {
                return ['status' => false, 'message' => "Recharge Incomplete, Please try again!"];
                // echo "Recharge Incomplete, Please try again!";
                //return  Redirect()->back()->with('error','Recharge Incomplete, Please try again!');
            }
        } else {
            return ['status' => false, 'message' => "Recharge Incomplete, Please try again!"];
            //echo "Recharge Incomplete, Please try again!";
            // return  Redirect()->back()->with('error','Recharge Incomplete, Please try again!');
        }
    }

    public function get_all_invoice(Request $request)
    {
        $start_date = Carbon::parse($request->start_date)->toDateTimeString();
        $end_date = Carbon::parse($request->end_date)->addDays(1)->toDateTimeString();
        $total_cost = 0;
        $total_reseller_profit = 0;
        $total_admin_profit = 0;
        $total_service_charge = 0;
        $total_own_profit = 0;
        $t = 0;
        $type = $request->type;
        $reseller_id = $request->retailer_id;
        if ($request->ajax()) {
            if (a::user()->role == 'admin') {
                if ($reseller_id) {
                    //file_put_contents('test.txt',$request->retailer_id);
                    if ($type == 'all') {
                        $data = RechargeHistory::where('reseller_id', $reseller_id)->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    } elseif ($type == 'International') {
                        $data = RechargeHistory::where('reseller_id', $reseller_id)->where('type', '!=', 'Domestic')->where('type', '!=', 'pin')->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    } else {
                        $data = RechargeHistory::where('reseller_id', $reseller_id)->where('type', '!=', 'International')->where('type', '!=', 'White Calling')->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    }
                } else {
                    if ($type == 'all') {
                        $data = RechargeHistory::whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    } elseif ($type == 'International') {
                        $data = RechargeHistory::where('type', '!=', 'Domestic')->where('type', '!=', 'pin')->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    } else {
                        $data = RechargeHistory::where('type', '!=', 'International')->where('type', '!=', 'White Calling')->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    }
                }

            // $total_cost = $data->sum('amount');
            // foreach ($data as $value) {
            //     if ($value->amount != 0) {
            //         $total_profit += $value->admin_com;
            //     } else {
            //         $total_profit += $value->discount;
            //     }
            // }
            } elseif (a::user()->role == 'reseller') {
                if ($type == 'all') {
                    $data = RechargeHistory::where('reseller_id', a::user()->id)->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                } elseif ($type == 'International') {
                    $data = RechargeHistory::where('type', '!=', 'Domestic')->where('type', '!=', 'pin')->where('reseller_id', a::user()->id)->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                } else {
                    $data = RechargeHistory::where('type', '!=', 'International')->where('type', '!=', 'White Calling')->where('reseller_id', a::user()->id)->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                }
            } else {
                if ($reseller_id) {
                    //file_put_contents('test.txt',$request->retailer_id);
                    if ($type == 'all') {
                        $data = RechargeHistory::where('reseller_id', $reseller_id)->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    } elseif ($type == 'International') {
                        $data = RechargeHistory::where('reseller_id', $reseller_id)->where('type', '!=', 'Domestic')->where('type', '!=', 'pin')->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    } else {
                        $data = RechargeHistory::where('reseller_id', $reseller_id)->where('type', '!=', 'International')->where('type', '!=', 'White Calling')->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    }
                } else {
                    if ($type == 'all') {
                        $data = RechargeHistory::whereHas('user', function ($query) {
                            $query->where('created_by', a::user()->id)
                                ->orWhere('id', a::user()->id);
                        })->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    } elseif ($type == 'International') {
                        $data = RechargeHistory::whereHas('user', function ($query) {
                            $query->where('created_by', a::user()->id)
                                ->orWhere('id', a::user()->id);
                        })->where('type', '!=', 'Domestic')->where('type', '!=', 'pin')->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    } else {
                        $data = RechargeHistory::whereHas('user', function ($query) {
                            $query->where('created_by', a::user()->id)
                                ->orWhere('id', a::user()->id);
                        })->where('type', '!=', 'International')->where('type', '!=', 'White Calling')->whereBetween('created_at', [$start_date, $end_date])->latest()->get(['*']);
                    }
                }
            }

            $total_cost = $data->sum('amount');
            $total_service_charge = $data->sum('service');
            $total_reseller_profit = $data->sum('reseller_com');
            $total_admin_profit = $data->sum('admin_com');
            $total_sub_profit = $data->sum('sub_profit');
            if (a::user()->role == 'sub') {
                $total_reseller_profit = 0;
                foreach ($data as $d) {
                    if ($d->reseller_id == a::user()->id) {
                        $total_own_profit += $d->reseller_com;
                    } else {
                        $total_reseller_profit += $d->reseller_com;
                    }
                }
            }

            // foreach ($data as $value) {
            //     $value->total_profit = round($total_profit, 2);
            //     $value->total_cost = round($total_cost, 2);
            // }
            if (sizeof($data) > 0) {
                $data[0]['total_cost'] = round($total_cost, 2);
                $data[0]['total_service_charge'] = round($total_service_charge, 2);
                $data[0]['total_reseller_profit'] = round($total_reseller_profit, 2);
                $data[0]['total_admin_profit'] = round($total_admin_profit, 2);
                $data[0]['total_sub_profit'] = round($total_sub_profit, 2);
                $data[0]['total_own_profit'] = round($total_own_profit, 2);
            }

            return Datatables::of($data)

                ->addIndexColumn()

                ->addColumn('number', function ($data) {
                    if ($data->number) {
                        return $data->number;
                    } else {
                        return $data->pin_number;
                    }
                })

            //  ->addColumn('total_cost', function($data){
            //     return $total_cost;

            //  })

                ->addColumn('date', function ($data) {
                    return Carbon::parse($data->created_at)->timezone('Europe/Rome')->format('d-m-Y H:i:s');
                })
                ->addColumn('reseller_name', function ($data) {
                    return $data->user ? $data->user->first_name . " " . $data->user->last_name . "(" . $data->user->user_id . ")" : '';
                })

                ->addColumn('recharge_type', function ($data) {
                    if (a::user()->role == 'admin') {
                        $text = $data->company_name;
                    } else {
                        $text = $data->type;
                    }

                    return $text;
                })
                ->addColumn('own_com', function ($data) {
                    $own_com = 0;
                    if ($data->reseller_id == a::user()->id) {
                        $own_com = $data->reseller_com;
                    }
                    return $own_com;
                })
                ->addColumn('sub_reseller_com', function ($data) {
                    $sub_reseller_com = 0;
                    if ($data->reseller_id != a::user()->id) {
                        $sub_reseller_com = $data->reseller_com;
                    }
                    return $sub_reseller_com;
                })
                ->addColumn('invoice', function ($data) {
                    $button = '<a class="btn btn-success" href="recharge_invoice/' . $data->id . '"> Invoice</a>';
                    return $button;
                })
                ->rawColumns(['invoice', 'own_com', 'sub_reseller_com'])
                ->make(true);
        }
    }
    public function invoices()
    {
        if (a::user()->role == 'admin') {
            $data = RechargeHistory::get();
            $cost = $data->sum('amount');
            $profit = 0;
            foreach ($data as $value) {
                if ($value->amount != 0) {
                    $profit += $value->admin_com;
                } else {
                    $profit += $value->discount;
                }
            }
        } else {
            $data = RechargeHistory::where('reseller_id', a::user()->id)->latest()->get();
            $cost = $data->sum('amount') + $data->sum('service');
            $profit = $data->sum('reseller_com');
        }

        $resellers = user::where('role', '!=', 'admin')->latest()->get();

        return view('front.print-all-invoice', compact('data', 'cost', 'profit', 'resellers'));
    }

    public function filebydate($start, $end)
    {
        $st = Carbon::parse($start)->toDateTimeString();
        $en = Carbon::parse($end)->toDateTimeString();
        // dd($start);
        if (a::user()->role == 'admin') {
            $data = RechargeHistory::whereBetween('created_at', [$start, $end])->get();
            $cost = $data->sum('amount');
            $profit = $data->sum('admin_com');
        } else {
            $data = RechargeHistory::whereBetween('created_at', [$start, $end])->where('reseller_id', a::user()->id)->get();
            $cost = $data->sum('cost');
            $profit = $data->sum('reseller_com');
        }
        // dd($data);
        return view('front.print-all-invoice', compact('data', 'cost', 'profit'));
    }

    public function pinfilebydate($start, $end)
    {
        $st = Carbon::parse($start)->toDateTimeString();
        $en = Carbon::parse($end)->toDateTimeString();
        // dd($start);
        if (a::user()->role == 'admin') {
            $data = Pin::whereBetween('created_at', [$start, $end])->get();
            $cost = $data->sum('amount');
            $profit = $data->sum('admin_com');
        } else {
            $data = Pin::whereBetween('created_at', [$start, $end])->where('reseller_id', a::user()->id)->get();
            $cost = $data->sum('cost');
            $profit = $data->sum('reseller_com');
        }
        // dd($data);
        return view('front.print-all-invoice_pin', compact('data', 'cost', 'profit'));
    }

    public function filebytype(Request $request)
    {
        if ($request->type == 'all') {
            if (a::user()->role == 'admin') {
                $data = RechargeHistory::latest()->get();
                $cost = $data->sum('amount');
                $profit = $data->sum('admin_com');
            } else {
                $data = RechargeHistory::where('reseller_id', a::user()->id)->get();
                $cost = $data->sum('cost');
                $profit = $data->sum('reseller_com');
            }
        } else {
            if (a::user()->role == 'admin') {
                $data = RechargeHistory::where('type', $request->type)->get();
                $cost = $data->sum('amount');
                $profit = $data->sum('admin_com');
            } else {
                $data = RechargeHistory::where('type', $request->type)->where('reseller_id', a::user()->id)->get();
                $cost = $data->sum('cost');
                $profit = $data->sum('reseller_com');
            }
        }
        return view('front.print-all-invoice', compact('data', 'cost', 'profit'));
    }
}
