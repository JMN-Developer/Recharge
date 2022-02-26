<?php

use App\Models\RechargeHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
// if (!function_exists('reseller_comission')) {
// function reseller_comission($discount,$percentage=50)
// {
//     $reseller_profit = round((($percentage/100)*$discount),2);
//     return $reseller_profit;
// }
// }

if (!function_exists('reseller_comission')) {
    function reseller_comission($amount)
    {
        if(auth()->user()->role == 'admin')
        {
            $percentage = 0;
        }
        else
        {
            $percentage = auth()->user()->admin_international_recharge_commission;

        }
        $percentage_amount = round((($percentage/100)*$amount),2);
        return $percentage_amount;
    }
    }


if (!function_exists('check_daily_duplicate')) {
    function check_daily_duplicate($number)
    {
        $change = [' ','+'];
        $number = str_replace($change,'',$number);
        $dt = Carbon::now();
        $current_date = $dt->toDateString();
        $avial = DB::table('recharge_histories')->where('created_at','LIKE','%'.$current_date)->where('number',$number)->first();
        if($avial)
        return false;
        else
        return true;
    }
    }

    if (!function_exists('euro_rate_for_bd_recharge')) {
        function euro_rate_for_bd_recharge()
        {


            $rate = DB::table('api_lists')->where('type','Bangladesh')->first()->euro_rate_per_hundred_bdt;
            return $rate;

        }
        }


if (!function_exists('transaction_cargo')) {
    function transaction_cargo($type)
    {
        $date = date('dmy');
        $resller_id = str_pad(auth()->user()->id, 4, "0", STR_PAD_LEFT);
        if($type=='Goods')
        {
            $type = 1;
        }
        else
        {
            $type = 2;
        }
        $order_date = date('Y-m-d');
        $order_count = DB::table('orders')->where('reseller_id',auth()->user()->id)->where('created_at','LIKE',$order_date.'%')->count();
        $order_count = $order_count+1;
        $order_count =  str_pad( $order_count, 2, "0", STR_PAD_LEFT);
        $transacrion_id = $date.$resller_id.$type.$order_count;
        return $transacrion_id;

    }
    }


    if (!function_exists('reseller_profit')) {
        function reseller_profit($amount)
        {
            return $amount;
        //    $extra_amount =  reseller_comission($amount);
        //     if(auth()->user()->role == 'admin')
        //     {
        //         $profit = 0;
        //     }
        //     else
        //     {
        //         $profit = auth()->user()->reseller_profit->international_recharge_profit;
        //         if(!$profit)
        //         {
        //             $profit = 20;
        //         }

        //     }
        //     $percentage_amount = round((($profit/100)*$amount),2);
        //     return $percentage_amount;
        }
        }
        // if (!function_exists('get_current_balance')) {
        //     function get_current_balance($amount)
        //     {
        //         if()
        //     }
        //     }

        if (!function_exists('reseller_profit_white_calling')) {
            function reseller_profit_white_calling($amount)
            {
            //    $extra_amount =  reseller_comission($amount);
                if(auth()->user()->role == 'admin')
                {
                    $profit = 0;
                }
                else
                {
                    $profit = 11;

                }
                $percentage_amount = round((($profit/100)*$amount),2);
                return $percentage_amount;
            }
            }

            if (!function_exists('reseller_profit_pin')) {
                function reseller_profit_pin($amount)
                {
                //    $extra_amount =  reseller_comission($amount);
                    if(auth()->user()->role == 'admin')
                    {
                        $profit = 0;
                    }
                    else
                    {
                        $profit = 65;

                    }
                    $percentage_amount = round((($profit/100)*$amount),2);
                    return $percentage_amount;
                }
                }


                if (!function_exists('reseller_profit_domestic')) {
                    function reseller_profit_domestic($amount)
                    {
                    //    $extra_amount =  reseller_comission($amount);
                        if(auth()->user()->role == 'admin')
                        {
                            $profit = 0;
                        }
                        else
                        {
                            $profit = 65;

                        }
                        $percentage_amount = round((($profit/100)*$amount),2);
                        return $percentage_amount;
                    }
                    }


