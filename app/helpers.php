<?php

use App\Models\RechargeHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        if (auth()->user()->role == 'admin') {
            $percentage = 0;
        } else {
            $percentage = auth()->user()->admin_international_recharge_commission;
        }

        $percentage_amount = round((($percentage / 100) * $amount), 2);
        return $percentage_amount;
    }
}

if (!function_exists('parent_comission')) {
    function parent_comission($amount)
    {
        $percentage = auth()->user()->parent->admin_international_recharge_commission;

        $percentage_amount = round((($percentage / 100) * $amount), 2);

        return $percentage_amount;
    }
}

if (!function_exists('sub_comission')) {
    function sub_comission($amount)
    {
        $percentage = auth()->user()->parent->admin_international_recharge_commission;

        $percentage_amount = round((($percentage / 100) * $amount), 2);

        return $percentage_amount;
    }
}

if (!function_exists('service_permission')) {
    function service_permission($service_name, $services)
    {
        foreach ($services as $key => $val) {
            if ($val['service_name'] === $service_name) {
                return $val['permission'];
            }
        }
        return null;
    }
}

if (!function_exists('check_daily_duplicate')) {
    function check_daily_duplicate($number)
    {
        $change = [' ', '+'];
        $number = str_replace($change, '', $number);
        $dt = Carbon::now();
        $current_date = $dt->toDateString();
        $avial = DB::table('recharge_histories')->where('created_at', 'LIKE', '%' . $current_date)->where('number', $number)->first();
        if ($avial) {
            return false;
        } else {
            return true;
        }

    }
}

if (!function_exists('euro_rate_for_bd_recharge')) {
    function euro_rate_for_bd_recharge()
    {

        $rate = DB::table('api_lists')->where('type', 'Bangladesh')->first()->euro_rate_per_hundred_bdt;
        return $rate;

    }
}

if (!function_exists('transaction_cargo')) {
    function transaction_cargo($type)
    {
        $date = date('dmy');
        $resller_id = str_pad(auth()->user()->id, 4, "0", STR_PAD_LEFT);
        if ($type == 'Goods') {
            $type = 1;
        } else {
            $type = 2;
        }
        $order_date = date('Y-m-d');
        $order_count = DB::table('orders')->where('reseller_id', auth()->user()->id)->where('created_at', 'LIKE', $order_date . '%')->count();
        $order_count = $order_count + 1;
        $order_count = str_pad($order_count, 2, "0", STR_PAD_LEFT);
        $transacrion_id = $date . $resller_id . $type . $order_count;
        return $transacrion_id;

    }
}

if (!function_exists('reseller_profit')) {
    function reseller_profit($amount)
    {
        if (auth()->user()->role == 'admin') {
            $profit = 0;
        } else {
            $profit = auth()->user()->admin_international_recharge_commission;
            if (!$profit) {
                $profit = 20;
            }
        }

        $percentage_amount = round((($profit / 100) * $amount), 2);
        return $percentage_amount;
    }
}

if (!function_exists('parent_profit')) {
    function parent_profit($amount)
    {
        if (auth()->user()->role == 'admin') {
            $profit = 0;
        } else {
            $profit = auth()->user()->parent->admin_international_recharge_commission;
            if (!$profit) {
                $profit = 20;
            }
        }

        $percentage_amount = round((($profit / 100) * $amount), 2);
        return $percentage_amount;
    }
}

if (!function_exists('sub_profit')) {
    function sub_profit($amount)
    {
        if (auth()->user()->role == 'admin') {
            $profit = 0;
        } else {
            $profit = auth()->user()->admin_international_recharge_commission;
            if (!$profit) {
                $profit = 20;
            }
        }

        $percentage_amount = round((($profit / 100) * $amount), 2);
        return $percentage_amount;
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
        if (auth()->user()->role == 'admin') {
            $profit = 0;
        } else {
            $profit = 11;

        }
        $percentage_amount = round((($profit / 100) * $amount), 2);
        return $percentage_amount;
    }
}

if (!function_exists('parent_profit_white_calling')) {
    function parent_profit_white_calling($amount)
    {
        //    $extra_amount =  reseller_comission($amount);
        if (auth()->user()->role == 'admin') {
            $profit = 0;
        } else {
            $profit = 11;

        }
        $percentage_amount = round((($profit / 100) * $amount), 2);
        return $percentage_amount;
    }
}

if (!function_exists('check_recurrent_recharge')) {
    function check_recurrent_recharge($number)
    {

        $recharge_history = RechargeHistory::where('number', $number)->latest()->first();
        if ($recharge_history) {
            $timeDiff = $recharge_history->created_at->diffInMinutes(Carbon::now());
            if ($timeDiff <= 10) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('reseller_profit_pin')) {
    function reseller_profit_pin($amount)
    {
        //    $extra_amount =  reseller_comission($amount);
        if (auth()->user()->role == 'admin') {
            $profit = 0;
        } else {
            $profit = 65;

        }
        $percentage_amount = round((($profit / 100) * $amount), 2);
        return $percentage_amount;
    }
}

if (!function_exists('parent_profit_pin')) {
    function parent_profit_pin($amount)
    {
        //    $extra_amount =  reseller_comission($amount);
        if (auth()->user()->role == 'admin') {
            $profit = 0;
        } else {
            $profit = 65;

        }
        $percentage_amount = round((($profit / 100) * $amount), 2);
        return $percentage_amount;
    }
}

if (!function_exists('parent_profit_domestic')) {
    function parent_profit_domestic($amount)
    {
        //    $extra_amount =  reseller_comission($amount);
        if (auth()->user()->role == 'admin') {
            $profit = 0;
        } else {
            $profit = auth()->user()->parent->admin_recharge_commission;

        }
        $percentage_amount = round((($profit / 100) * $amount), 2);
        return $percentage_amount;
    }
}

if (!function_exists('reseller_profit_domestic')) {
    function reseller_profit_domestic($amount)
    {
        //    $extra_amount =  reseller_comission($amount);
        if (auth()->user()->role == 'admin') {
            $profit = 0;
        } else {
            $profit = auth()->user()->admin_recharge_commission;

        }
        $percentage_amount = round((($profit / 100) * $amount), 2);
        return $percentage_amount;
    }
}

// if (!function_exists('reseller_profit_domestic')) {
//     function reseller_profit_domestic($amount)
//     {
//     //    $extra_amount =  reseller_comission($amount);
//         if(auth()->user()->role == 'admin')
//         {
//             $profit = 0;
//         }
//         else if(auth()->user()->role =='user')
//         {
//             $profit = auth()->user()->admin_recharge_commission;

//         }
//         else{
//             $profit = auth()->user()->parent->admin_recharge_commission;
//         }
//         if(auth()->user()->role == 'reseller'){
//             $percentage_amount = round((($profit/100)*$amount),2);
//             $profit = auth()->user()->admin_recharge_commission;
//             $percentage_amount = round((($profit/100)*$percentage_amount),2);
//         }
//         else{
//             $percentage_amount = round((($profit/100)*$amount),2);
//         }

//         return $percentage_amount;
//     }
//     }
