<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\RechargeHistory;
use App\Services\BangladeshiRecharge;
use App\Services\CheckRechargeAvail;
use App\Services\DtOneProvider;
use App\Services\GenerateTransactionId;
use App\Services\UpdateWallet;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as a;
use Illuminate\Support\Facades\Log;

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
    public function index()
    {
        if (a::user()->role == 'admin') {
            $data = RechargeHistory::where(function ($q) {
                $q->where('type', 'International')
                    ->orWhere('type', 'Bangladesh');
            })->join('users', 'users.id', '=', 'recharge_histories.reseller_id')
                ->select('recharge_histories.*', 'users.nationality')
                ->latest()
                ->take(5)
                ->get();
        } else {
            $data = RechargeHistory::where('reseller_id', a::user()->id)->where(function ($q) {
                $q->where('type', 'International')
                    ->orWhere('type', 'Bangladesh');
            })->latest()->take(5)->get();
        }

        return view('front.recharge-dtone', compact('data'));
    }
    public function make_sku_list($skus)
    {
        Log::info(json_encode($skus));
        $data = array();
        foreach ($skus as $sku) {
            $discount = $sku->prices->retail->amount - $sku->prices->wholesale->amount;
            if (auth()->user()->parent->role == 'sub') {
                $amount_text = $sku->prices->retail->amount - reseller_profit(parent_profit($discount)) . "Euro &nbsp(&nbsp" . $sku->name . ")";
                array_push($data, ['skuId' => $sku->id, 'amount' => $sku->prices->retail->amount - reseller_profit(parent_profit($discount)), 'amount_text' => $amount_text, 'bd_amount' => $sku->destination->amount]);
            } else {
                $amount_text = $sku->prices->retail->amount - reseller_profit($discount) . "Euro &nbsp(&nbsp" . $sku->name . ")";
                array_push($data, ['skuId' => $sku->id, 'amount' => $sku->prices->retail->amount - reseller_profit($discount), 'amount_text' => $amount_text, 'bd_amount' => $sku->destination->amount]);
            }
        }
        usort($data, function ($a, $b) {
            return $a['amount'] <=> $b['amount'];
        });
        return $data;
    }
    public function all_transaction()
    {
        $data = $this->dtone->transaction_list();
        $record = [];
        foreach ($data as $d) {
            if (str_contains($d->creation_date, '2022-03-04') || str_contains($d->creation_date, '2022-03-05') || str_contains($d->creation_date, '2022-03-06')) {
                array_push($record, ['date' => $d->creation_date, 'external_id' => $d->external_id, 'amount' => $d->prices->retail->amount]);
            }
        }
        return json_encode($record);
    }

    public function make_bundle_data_list($list)
    {
        $data = array();
        foreach ($list as $l) {
            array_push($data, ['description' => $l->description, 'skuId' => $l->id, 'amount' => round($l->prices->retail->amount, 2), 'validity' => $l->validity->quantity . ' ' . $l->validity->unit]);
        }
        usort($data, function ($a, $b) {
            return $a['amount'] <=> $b['amount'];
        });
        return $data;
    }

    public function make_internet_data_list($list)
    {
        $data = array();
        foreach ($list as $l) {
            array_push($data, ['description' => $l->description, 'skuId' => $l->id, 'amount' => round($l->prices->retail->amount, 2), 'validity' => $l->validity->quantity . ' ' . $l->validity->unit]);
        }
        usort($data, function ($a, $b) {
            return $a['amount'] <=> $b['amount'];
        });
        return $data;
    }
    public function mobile_number_details(Request $request)
    {
        $number = $request->number;
        $countryIso = $request->countryIso;
        try {
            if (str_contains($number, '+880')) {
                $change = [' ', '+'];
                $number = str_replace($change, '', $number);
                $rate = euro_rate_for_bd_recharge();
                $unit_rate = $rate / 100;
                $operator_details = $this->bangladeshi_recharge->operatorInfo($number);
                $bd_offer_data = $this->bangladeshi_recharge->offer_details($operator_details['data']->operator_id);
                $operator_name = $operator_details['data']->operator_name;
                // file_put_contents('test.txt',$operator_details['data']->operator_name);
                foreach ($bd_offer_data as $d) {
                    $d->update_amount = round($d->amount * $unit_rate, 4);
                    $d->operator_logo = DB::table('sim_operators')->where('operator', $operator_details['data']->operator_name)->first()->img;
                }
                return ['status' => true, 'operator_name' => $operator_name, 'exchange_rate' => $unit_rate, 'bd_offer_data' => $bd_offer_data];
            } else {
                $data = $this->dtone->lookup($number);
                if ($data['status']) {
                    $data = $data['payload'];
                    $credit_data = [];
                    $internet_data = [];
                    $combo_data = [];
                    for ($i = 0; $i < sizeof($data); $i++) {
                        if ($data[$i]->tags[0] == 'AIRTIME') {
                            array_push($credit_data, $data[$i]);
                        }

                        if ($data[$i]->tags[0] == 'BUNDLE') {
                            array_push($combo_data, $data[$i]);
                        }

                        if ($data[$i]->tags[0] == 'DATA') {
                            array_push($internet_data, $data[$i]);
                        }
                    }
                    // file_put_contents('test.txt',sizeof($credit_data));
                    $operator_name = $data[0]->operator->name;
                    $skus = $this->make_sku_list($credit_data);
                    $internet_data = $this->make_internet_data_list($internet_data);
                    $combo_data = $this->make_bundle_data_list($combo_data);
                    //file_put_contents('test.txt',json_encode($combo_data));
                    return ['status' => true, 'data' => $data, 'operator_name' => $operator_name, 'skus' => $skus, 'internet' => $internet_data, 'combo' => $combo_data];
                } else {
                    return ['status' => false, 'message' => $data['payload']->errors[0]->message];
                }
            }
        } catch (Exception $e) {
            return ['status' => false, 'message' => 'Some error occured. Please try after sometimes'];
        }
    }

    public function create_recharge($data, $number, $txid, $country_code, $service = 0)
    {
        $discount = $data->prices->retail->amount - $data->prices->wholesale->amount;

        if (auth()->user()->parent->role == 'sub') {
            $parent_profit = parent_profit($discount);
            $reseller_profit = reseller_profit($parent_profit);
            $admin_profit = $discount - $parent_profit;
            $sub_profit = $parent_profit - $reseller_profit;
            $total_amount = $data->prices->retail->amount; //14.4
        } else {
            $reseller_profit = reseller_profit($discount); // (.26) = .13
            $admin_profit = $discount - $reseller_profit; //2+1-1.1 = 1.9
            $sub_profit = 0;
            $total_amount = $data->prices->retail->amount; //11 = 11-1.1 = 9.9
        }

        $log_data = 'Number = ' . $number . ' Amount = ' . $data->prices->retail->amount . ' R-Com = ' . $reseller_profit . ' A-Com = ' . $admin_profit . ' TXID = ' . $txid;
        Log::channel('rechargelog')->info($log_data);
        $recharge = RechargeHistory::create([
            'reseller_id' => a::user()->id,
            'number' => $number,
            'amount' => $total_amount,
            'txid' => $txid,
            'type' => 'International',
            'operator' => $data->product->operator->name,
            'status' => 'completed',
            'cost' => round($data->prices->wholesale->amount, 2),
            'transaction_id_company' => $data->id,
            'country_code' => $country_code,
            'discount' => $discount,
            'service' => $service,
            'reseller_com' => $reseller_profit,
            'admin_com' => $admin_profit,
            'deliveredAmount' => floor($data->benefits[0]->amount->total_excluding_tax),
            'deliveredAmountCurrencyCode' => $data->benefits[0]->unit,
            'company_name' => 'International4',
            'sub_profit' => $sub_profit,
            'recharge_comission' => Auth::user()->admin_international_recharge_commission,

        ]);
        return $recharge;
    }
    public function create_recharge_bangladesh($number, $amount, $updated_amount, $txid, $operator_name, $transaction_id_company, $service = 0)
    {
        $admin_profit = $this->calculate_profit($amount);
        $cost = $amount - $admin_profit;
        $log_data = 'Number = ' . $number . ' Amount = ' . $amount . ' R-Com = ' . $service . ' A-Com = ' . $admin_profit . ' TXID = ' . $txid;
        Log::channel('rechargelog')->info($log_data);
        $recharge = RechargeHistory::create([
            'reseller_id' => Auth::user()->id,
            'number' => $number,
            'amount' => $amount,
            'txid' => $txid,
            'type' => 'Bangladesh',
            'operator' => $operator_name,
            'status' => 'completed',
            'cost' => $cost,
            'transaction_id_company' => $transaction_id_company,
            'service' => $service,
            'admin_com' => $admin_profit,
            'deliveredAmount' => $updated_amount,
            'deliveredAmountCurrencyCode' => 'BDT',
            'company_name' => 'Bangladesh1',
            'recharge_comission' => Auth::user()->admin_international_recharge_commission,

        ]);
        return $recharge;
    }
    public function calculate_profit($amount)
    {
        $rate = euro_rate_for_bd_recharge();
        $current_currency_rate = $this->bangladeshi_recharge->current_euro_rate();
        $currency_profit = $rate - (100 / $current_currency_rate);
        $api_profit = (1.5 / 100) * $amount;
        // file_put_contents('test.txt',$current_currency_rate.' '.$currency_profit.' '.$company_profit);
        $admin_profit = $currency_profit + $api_profit;
        return $admin_profit;
    }
    public function update_balance($recharge_amount, $cost)
    {
        $balance_info = Balance::where('type', 'dtone')->first();
        $current_balance = $balance_info->balance - $cost;
        Balance::where('type', 'dtone')->update(['balance' => $current_balance]);
    }
    public function update_balance_bangladesh()
    {
        $current_balance = $this->bangladeshi_recharge->balanceInfo();

        Balance::where('type', 'ssl')->update(['balance' => $current_balance['balance_info']]);
    }

    public function bangladeshi_recharge($number, $request)
    {
        try {
            $change = [' ', '+'];
            $number = str_replace($change, '', $number);
            $rate = euro_rate_for_bd_recharge();
            $unit_rate = $rate / 100;
            $amount = round($request->bd_amount * $unit_rate, 3);
            if (!check_recurrent_recharge($number)) {
                return ['status' => false, 'message' => 'You can not recharge with same number within 10 minutes!'];
            }

            if (!CheckRechargeAvail::check($amount, 'International')) {
                return ['status' => false, 'message' => 'Insufficient wallet & Limit. Please contact with admin'];
            }
            $operator_details = $this->bangladeshi_recharge->operatorInfo($number);
            if ($operator_details['soap_exception_occured'] == false) {
                //file_put_contents('test.txt',$amount.);

                $operator_id = $operator_details['data']->operator_id;
                $operator_name = $operator_details['data']->operator_name;
                $guid = new GenerateTransactionId(Auth::user()->id, 13);
                $guid = $guid->transaction_id();

                $create_recharge = $this->bangladeshi_recharge->CreateRecharge($guid, $operator_id, $number, $request->bd_amount);
                if ($create_recharge['data']->recharge_status == 100) {
                    $init_recharge = $this->bangladeshi_recharge->InitRecharge($guid, $create_recharge['data']->vr_guid);
                    if ($init_recharge['data']->recharge_status == 200) {
                        $recharge = $this->create_recharge_bangladesh($number, $amount, $request->bd_amount, $guid, $operator_name, $create_recharge['data']->vr_guid, $request->service_charge);
                        UpdateWallet::update($recharge);
                        $this->update_balance_bangladesh();
                        return ['status' => true, 'message' => 'Recharge Successfull'];
                    } else {
                        return ['status' => false, 'message' => $init_recharge['data']->message];
                    }
                } else {
                    return ['status' => false, 'message' => $create_recharge['data']->message];
                }
            } else {
                return ['status' => false, 'message' => $operator_details['exception']];
            }
        } catch (Exception $e) {
            return ['status' => false, 'message' => 'Some error occured. Please try again after sometimes'];
        }
    }

    public function recharge(Request $request)
    {
        //file_put_contents('test.txt',$request->amount);
        //file_put_contents('test.txt',$test);

        $country_code = $request->countryCode;
        // file_put_contents('test.txt',$country_code);
        //return;
        $number = $request->number;
        if (!check_recurrent_recharge($number)) {
            return ['status' => false, 'message' => 'You can not recharge with same number within 10 minutes!'];
        }
        //  file_put_contents('test.txt',$request->bd_amount);
        //return;
        if (str_contains($number, '+880')) {
            $data = $this->bangladeshi_recharge($number, $request);
            return ['status' => $data['status'], 'message' => $data['message']];
        }
        if (!CheckRechargeAvail::check($request->amount, 'International')) {
            return ['status' => false, 'message' => 'Insufficient wallet & Limit. Please contact with admin'];
        }

        $skuId = $request->id;
        $transaction = new GenerateTransactionId(a::user()->id, 12);
        $txid = $transaction->transaction_id();
        // file_put_contents('test.txt',$skuId);
        // return;
        $data = $this->dtone->recharge($skuId, $txid, $number);

        //  //file_put_contents('test.txt',$tmp_data->responseCode);

        if ($data['status']) {
            $recharge = $this->create_recharge($data['payload'], $number, $txid, $country_code, $request->service_charge);
            UpdateWallet::update($recharge);
            $this->update_balance($data['payload']->prices->retail->amount, $data['payload']->prices->wholesale->amount);
            return ['status' => true, 'message' => 'Recharge Successfull'];
        } else {
            return ['status' => false, 'message' => $data['message']];
        }
    }
}
