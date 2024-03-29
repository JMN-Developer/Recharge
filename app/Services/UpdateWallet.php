<?php

namespace App\Services;

use App\Models\RechargeHistory;
use App\Models\TransactionHistory;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Class UpdateWallet
 * @package App\Services
 */
class UpdateWallet
{
    public static function create_transaction($id, $transaction_type, $transaction_source, $wallet_before_transaction, $wallet_after_transaction, $amount, $wallet_type, $reseller_id, $parent_id = null)
    {
        if ($transaction_source == 'International') {
            $service_code = 1;
            $transaction_wallet = 'International';
        } elseif ($transaction_source == 'Domestic') {
            $service_code = 2;
            $transaction_wallet = 'Domestic';
        } elseif ($transaction_source == 'pin') {
            $service_code = 3;
            $transaction_wallet = 'Domestic';
        } elseif ($transaction_source == 'White Calling') {
            $service_code = 4;
            $transaction_wallet = 'International';
        } elseif ($transaction_source == 'Sim') {
            $service_code = 5;

            $transaction_wallet = 'Sim';
        } elseif ($transaction_source == 'Cargo') {
            $service_code = 6;
            $transaction_wallet = 'Cargo';
        } elseif ($transaction_source == 'Bangladesh') {
            $service_code = 7;
            $transaction_wallet = 'International';
        }

        // else if($transaction_source =='Wallet Request')
        // {
        //     $service_code = 7;
        //     $transaction_wallet = 'Wallet Request';
        // }

        $transaction_id = date('dmyHis') . str_pad($reseller_id, 4, "0", STR_PAD_LEFT) . str_pad($service_code, 2, "0", STR_PAD_LEFT);
        $log_data = 'TXID = ' . $transaction_id . ' Amount = ' . $amount . ' Tx-Type = ' . $transaction_type . ' WBT = ' . $wallet_before_transaction . ' WAT = ' . $wallet_after_transaction . ' Wallet Type = ' . $wallet_type;
        Log::channel('transactionlog')->info($log_data);
        TransactionHistory::create(['reseller_id' => $reseller_id, 'transaction_id' => $transaction_id, 'transaction_source_id' => $id, 'transaction_type' => $transaction_type, 'transaction_source' => $transaction_source, 'amount' => $amount, 'transaction_wallet' => $transaction_wallet, 'wallet_before_transaction' => $wallet_before_transaction, 'wallet_after_transaction' => $wallet_after_transaction, 'wallet_type' => $wallet_type, 'parent_id' => $parent_id,

        ]);
    }

    public static function update_agent($recharge)
    {
        if ($recharge->type == 'International' || $recharge->type == 'White Calling' || $recharge->type == 'Bangladesh') {

            $total_cost = $recharge->amount - $recharge->reseller_com - $recharge->sub_profit; //14.3-4.29-0.11 = 9.9
            $user_info = User::where('id', auth()->user()->parent
                    ->id)
                    ->first();

            $current_balance = $user_info->wallet;
            $current_limit_usage = $user_info->limit_usage;

            // file_put_contents('test.txt',$discount." ".$reseller_profit." ".$total_cost." ".$current_balance." ".$updated_balance);
            if ($current_balance < $total_cost) {
                if ($current_balance <= 0) {
                    $wallet_before_transaction = $user_info->limit_usage;

                    $user = User::find(auth()->user()->created_by);
                    $user->limit_usage = $current_limit_usage + $total_cost;
                    $user->save();

                    $wallet_after_transaction = $user->limit_usage;
                    RechargeHistory::where('id', $recharge->id)
                        ->update(['sub_recharge_source' => 'Limit']);
                    UpdateWallet::create_transaction($recharge->id, 'Debit (Recharege By ' . $recharge->user->user_id . ')', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost, 'limit', $user->id, $user->parent->id);

                } else {
                    $wallet_deduct = $total_cost - $current_balance;
                    $wallet_before_transaction = $user_info->wallet;

                    $user = User::find(auth()->user()->created_by);
                    $user->limit_usage = $current_limit_usage + $total_cost;
                    $user->wallet = 0;
                    $user->save();

                    $wallet_after_transaction = 0;
                    RechargeHistory::where('id', $recharge->id)
                        ->update(['sub_recharge_source' => 'Limit:' . $wallet_deduct . ',' . 'Wallet:' . $current_balance]);
                    UpdateWallet::create_transaction($recharge->id, 'Debit (Recharege By ' . $recharge->user->user_id . ')', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost - $wallet_before_transaction, 'main wallet', $user->id, $user->parent->id);
                    $wallet_before_transaction = 0;
                    $wallet_after_transaction = auth()->user()->limit_usage;
                    UpdateWallet::create_transaction($recharge->id, 'Debit (Recharege By ' . $recharge->user->user_id . ')', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost - $wallet_after_transaction, 'limit', $user->id, $user->parent->id);
                }
            } else {
                $updated_balance = $current_balance - $total_cost;
                //   $user = User::where('id',auth()->user()->id)->updateOrCreate(['wallet'=>$updated_balance]);
                $wallet_before_transaction = $user_info->wallet;

                $user = User::find(auth()->user()->created_by);
                $user->wallet = $updated_balance;
                $user->save();

                $wallet_after_transaction = $user->wallet;

                RechargeHistory::where('id', $recharge->id)
                    ->update(['sub_recharge_source' => 'Wallet']);
                UpdateWallet::create_transaction($recharge->id, 'Debit (Recharege By ' . $recharge->user->user_id . ')', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost, 'wallet', $user->id, $user->parent->id);
            }

        } else {

            $total_cost = $recharge->amount - ($recharge->reseller_com + $recharge->sub_profit);

            $user_info = User::where('id', auth()->user()->parent
                    ->id)
                    ->first();

            $current_balance = $user_info->domestic_wallet;
            $current_limit_usage = $user_info->domestic_limit_usage;
            $updated_balance = $current_balance - $total_cost;
            // file_put_contents('test.txt',$discount." ".$reseller_profit." ".$total_cost." ".$current_balance." ".$updated_balance);
            if ($current_balance < $total_cost) {
                if ($current_balance <= 0) {
                    $wallet_before_transaction = $user_info->domestic_limit_usage;

                    $user = User::find(auth()->user()->created_by);
                    $user->domestic_limit_usage = $current_limit_usage + $total_cost;
                    $user->save();
                    $wallet_after_transaction = $user->domestic_limit_usage;
                    //$user = User::where('id',auth()->user()->id)->updateOrCreate(['limit_usage'=>$current_limit_usage+$total_cost]);
                    RechargeHistory::where('id', $recharge->id)
                        ->update(['recharge_source' => 'Limit']);
                    UpdateWallet::create_transaction($recharge->id, 'Debit (Recharege By ' . $recharge->user->user_id . ')', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost, 'limit', $user->id, $user->parent->id);

                } else {
                    $wallet_deduct = $total_cost - $current_balance;
                    $wallet_before_transaction = $user_info->domestic_wallet;
                    // $user = User::where('id',auth()->user()->id)->updateOrCreate(['limit_usage'=>$current_limit_usage+$wallet_deduct,'wallet'=>0]);

                    $user = User::find(auth()->user()->created_by);
                    $user->domestic_limit_usage = $current_limit_usage + $wallet_deduct;
                    $user->domestic_wallet = 0;
                    $user->save();

                    $wallet_after_transaction = 0;
                    RechargeHistory::where('id', $recharge->id)
                        ->update(['recharge_source' => 'Limit:' . $wallet_deduct . ',' . 'Wallet:' . $current_balance]);
                    UpdateWallet::create_transaction($recharge->id, 'Debit (Recharege By ' . $recharge->user->user_id . ')', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost - $wallet_before_transaction, 'main wallet', $user->id, $user->parent->id, );
                    $wallet_before_transaction = 0;
                    $wallet_after_transaction = auth()->user()->domestic_limit_usage;
                    UpdateWallet::create_transaction($recharge->id, 'Debit (Recharege By ' . $recharge->user->user_id . ')', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost - $wallet_after_transaction, 'limit', $user->id, $user->parent->id);
                }
            } else {

                //   $user = User::where('id',auth()->user()->id)->updateOrCreate(['wallet'=>$updated_balance]);
                $wallet_before_transaction = auth()->user()->domestic_wallet;

                $user = User::find(auth()->user()->created_by);
                $user->domestic_wallet = $updated_balance;
                $user->save();

                $wallet_after_transaction = $user->domestic_wallet;

                RechargeHistory::where('id', $recharge->id)
                    ->update(['recharge_source' => 'Wallet']);
                UpdateWallet::create_transaction($recharge->id, 'Debit (Recharege By ' . $recharge->user->user_id . ')', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost, 'wallet', $user->id, $user->parent->id);
            }
        }
    }

    public static function update($recharge)
    {

        if (auth()->user()->role != 'admin') {
            if (auth()->user()->parent->role == 'sub') {
                self::update_agent($recharge);
            }
            if ($recharge->type == 'International' || $recharge->type == 'White Calling' || $recharge->type == 'Bangladesh') {
                $total_cost = $recharge->amount - $recharge->reseller_com; //reseller_com = reseller_profit

                $user_info = User::where('id', auth()->user()
                        ->id)
                        ->first();

                $current_balance = $user_info->wallet;
                $current_limit_usage = $user_info->limit_usage;

                // file_put_contents('test.txt',$discount." ".$reseller_profit." ".$total_cost." ".$current_balance." ".$updated_balance);
                if ($current_balance < $total_cost) {
                    if ($current_balance <= 0) {
                        $wallet_before_transaction = auth()->user()->limit_usage;
                        $user = User::find(auth()->user()->id);
                        $user->limit_usage = $current_limit_usage + $total_cost;
                        $user->save();
                        $wallet_after_transaction = $user->limit_usage;
                        RechargeHistory::where('id', $recharge->id)
                            ->update(['recharge_source' => 'Limit']);
                        UpdateWallet::create_transaction($recharge->id, 'Debit', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost, 'limit', $user->id, $user->parent->id);

                    } else {
                        $wallet_deduct = $total_cost - $current_balance;
                        $wallet_before_transaction = auth()->user()->wallet;
                        // $user = User::where('id',auth()->user()->id)->updateOrCreate(['limit_usage'=>$current_limit_usage+$wallet_deduct,'wallet'=>0]);

                        $user = User::find(auth()->user()->id);
                        $user->limit_usage = $current_limit_usage + $wallet_deduct;
                        $user->wallet = 0;
                        $user->save();

                        $wallet_after_transaction = 0;
                        RechargeHistory::where('id', $recharge->id)
                            ->update(['recharge_source' => 'Limit:' . $wallet_deduct . ',' . 'Wallet:' . $current_balance]);
                        UpdateWallet::create_transaction($recharge->id, 'Debit', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost - $wallet_before_transaction, 'main wallet', $user->id, $user->parent->id);
                        $wallet_before_transaction = 0;
                        $wallet_after_transaction = auth()->user()->limit_usage;
                        UpdateWallet::create_transaction($recharge->id, 'Debit', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost - $wallet_after_transaction, 'limit', $user->id, $user->parent->id);
                    }
                } else {
                    $updated_balance = $current_balance - $total_cost;
                    //   $user = User::where('id',auth()->user()->id)->updateOrCreate(['wallet'=>$updated_balance]);
                    $wallet_before_transaction = auth()->user()->wallet;

                    $user = User::find(auth()->user()->id);
                    $user->wallet = $updated_balance;
                    $user->save();

                    $wallet_after_transaction = $user->wallet;

                    RechargeHistory::where('id', $recharge->id)
                        ->update(['recharge_source' => 'Wallet']);
                    UpdateWallet::create_transaction($recharge->id, 'Debit', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost, 'wallet', $user->id, $user->parent->id);
                }

            } else {

                $total_cost = $recharge->amount - $recharge->reseller_com; // 10-0.09 = 9.91

                $user_info = User::where('id', auth()->user()
                        ->id)
                        ->first();

                $current_balance = $user_info->domestic_wallet;
                $current_limit_usage = $user_info->domestic_limit_usage;
                $updated_balance = $current_balance - $total_cost;
                // file_put_contents('test.txt',$discount." ".$reseller_profit." ".$total_cost." ".$current_balance." ".$updated_balance);
                if ($current_balance < $total_cost) {
                    if ($current_balance <= 0) {
                        $wallet_before_transaction = auth()->user()->domestic_limit_usage;

                        $user = User::find(auth()->user()->id);
                        $user->domestic_limit_usage = $current_limit_usage + $total_cost;
                        $user->save();

                        $wallet_after_transaction = $user->domestic_limit_usage;
                        //$user = User::where('id',auth()->user()->id)->updateOrCreate(['limit_usage'=>$current_limit_usage+$total_cost]);
                        RechargeHistory::where('id', $recharge->id)
                            ->update(['recharge_source' => 'Limit']);
                        UpdateWallet::create_transaction($recharge->id, 'Debit', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost, 'limit', $user->id, $user->parent->id);

                    } else {
                        $wallet_deduct = $total_cost - $current_balance;
                        $wallet_before_transaction = auth()->user()->domestic_wallet;
                        // $user = User::where('id',auth()->user()->id)->updateOrCreate(['limit_usage'=>$current_limit_usage+$wallet_deduct,'wallet'=>0]);

                        $user = User::find(auth()->user()->id);
                        $user->domestic_limit_usage = $current_limit_usage + $wallet_deduct;
                        $user->domestic_wallet = 0;
                        $user->save();
                        $wallet_after_transaction = 0;
                        RechargeHistory::where('id', $recharge->id)
                            ->update(['recharge_source' => 'Limit:' . $wallet_deduct . ',' . 'Wallet:' . $current_balance]);
                        UpdateWallet::create_transaction($recharge->id, 'Debit', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost - $wallet_before_transaction, 'main wallet', $user->id, $user->parent->id);
                        $wallet_before_transaction = 0;
                        $wallet_after_transaction = auth()->user()->domestic_limit_usage;
                        UpdateWallet::create_transaction($recharge->id, 'Debit', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost - $wallet_after_transaction, 'limit', $user->id, $user->parent->id);
                    }
                } else {

                    //   $user = User::where('id',auth()->user()->id)->updateOrCreate(['wallet'=>$updated_balance]);
                    $wallet_before_transaction = auth()->user()->domestic_wallet;
                    $user = User::find(auth()->user()->id);
                    $user->domestic_wallet = $updated_balance;
                    $user->save();

                    $wallet_after_transaction = $user->domestic_wallet;

                    RechargeHistory::where('id', $recharge->id)
                        ->update(['recharge_source' => 'Wallet']);
                    UpdateWallet::create_transaction($recharge->id, 'Debit', $recharge->type, $wallet_before_transaction, $wallet_after_transaction, $total_cost, 'wallet', $user->id, $user->parent->id);
                }
            }

        }

    }

    public static function updateBusCredit($data)
    {
        $user = User::find(Auth::user()->id); // Assuming you have the user object available

        $totalPrice = $data['total_price'];
        $busCreditProfit = $user->bus_credit_profit / 100; // Divide by 100 to get the decimal value
        $busCredit = $user->bus_credit - ($totalPrice * 0.07) + (($totalPrice * 0.07) * $busCreditProfit);

        $user->update([
            'bus_credit' => $busCredit,
        ]);
    }

}
