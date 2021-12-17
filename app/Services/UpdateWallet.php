<?php

namespace App\Services;
use App\Models\User;

/**
 * Class UpdateWallet
 * @package App\Services
 */
class UpdateWallet
{
    public static function update($recharge_amount,$actual_amount,$percentage=50)
    {
        if(auth()->user()->role != 'admin')
        {
        $discount = $recharge_amount-$actual_amount;
        $reseller_profit = round((($percentage/100)*$discount),2);
        $user_info =  User::where('id',auth()->user()->id)->first();
        $total_cost = $recharge_amount-$reseller_profit;
        $current_balance = $user_info->wallet;
        $current_limit_usage = $user_info->limit_usage;
        $updated_balance = $current_balance-$total_cost;

        if($current_balance < $recharge_amount)
        {
            if($current_balance == 0 )
            {

                User::where('id',auth()->user()->id)->update(['limit_usage'=>$current_limit_usage+$total_cost]);
            }
            else{
            $wallet_deduct = $recharge_amount-$current_balance;
            //$updated_balance = $current_balance-$wallet_deduct;
            User::where('id',auth()->user()->id)->update(['limit_usage'=>$current_limit_usage+$wallet_deduct,'wallet'=>0]);
            }
        }
        else
        {

        User::where('id',auth()->user()->id)->update(['wallet'=>$updated_balance]);
        }

    }

    }
}
