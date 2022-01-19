<?php

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


