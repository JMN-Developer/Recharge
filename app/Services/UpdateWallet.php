<?php

namespace App\Services;

use App\Models\RechargeHistory;
use App\Models\User;
use DB;

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
                RechargeHistory::where('id',$recharge->id)->update(['balance_before_recharge'=>auth()->user()->limit_usage]);
                $user = tap(DB::table('users')->where('id', auth()->user()->id)) ->update(['limit_usage'=>$current_limit_usage+$total_cost])->first();

                //$user = User::where('id',auth()->user()->id)->updateOrCreate(['limit_usage'=>$current_limit_usage+$total_cost]);
                RechargeHistory::where('id',$recharge->id)->update(['recharge_source'=>'Limit','balance_after_recharge'=>$user->limit_usage]);

            }
            else{
            $wallet_deduct = $total_cost-$current_balance;
            RechargeHistory::where('id',$recharge->id)->update(['balance_before_recharge'=>auth()->user()->wallet]);
           // $user = User::where('id',auth()->user()->id)->updateOrCreate(['limit_usage'=>$current_limit_usage+$wallet_deduct,'wallet'=>0]);
            $user = tap(DB::table('users')->where('id', auth()->user()->id)) ->update(['limit_usage'=>$current_limit_usage+$wallet_deduct,'wallet'=>0])->first();
            RechargeHistory::where('id',$recharge->id)->update(['recharge_source'=>'Limit:'.$wallet_deduct.','.'Wallet:'.$current_balance,'balance_after_recharge'=>$user->limit_usage]);
            }
        }
        else
        {
        RechargeHistory::where('id',$recharge->id)->update(['balance_before_recharge'=>auth()->user()->wallet]);
     //   $user = User::where('id',auth()->user()->id)->updateOrCreate(['wallet'=>$updated_balance]);
        $user = tap(DB::table('users')->where('id', auth()->user()->id)) ->update(['wallet'=>$updated_balance])->first();
        RechargeHistory::where('id',$recharge->id)->update(['recharge_source'=>'Wallet','balance_after_recharge'=>$user->wallet]);
        }

    }

    }
}
