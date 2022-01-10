<?php

namespace App\Services;

use App\Models\RechargeHistory;
use App\Models\User;

/**
 * Class UpdateWallet
 * @package App\Services
 */
class UpdateWallet
{
    public static function update($recharge)
    {
        if(auth()->user()->role != 'admin')
        {

        $total_cost = $recharge->amount-$recharge->reseller_com;

        $user_info =  User::where('id',auth()->user()->id)->first();

        $current_balance = $user_info->wallet;
        $current_limit_usage = $user_info->limit_usage;
        $updated_balance = $current_balance-$total_cost;
       // file_put_contents('test.txt',$discount." ".$reseller_profit." ".$total_cost." ".$current_balance." ".$updated_balance);
        if($current_balance < $total_cost)
        {
            if($current_balance <= 0 )
            {

                User::where('id',auth()->user()->id)->update(['limit_usage'=>$current_limit_usage+$total_cost]);
                RechargeHistory::where('id',$recharge->id)->update(['recharge_source'=>'Limit']);

            }
            else{
            $wallet_deduct = $total_cost-$current_balance;

            //$updated_balance = $current_balance-$wallet_deduct;
            User::where('id',auth()->user()->id)->update(['limit_usage'=>$current_limit_usage+$wallet_deduct,'wallet'=>0]);
            RechargeHistory::where('id',$recharge->id)->update(['recharge_source'=>'Limit:'.$wallet_deduct.','.'Wallet:'.$current_balance]);
            }
        }
        else
        {

        User::where('id',auth()->user()->id)->update(['wallet'=>$updated_balance]);
        RechargeHistory::where('id',$recharge->id)->update(['recharge_source'=>'Wallet']);
        }

    }

    }
}
