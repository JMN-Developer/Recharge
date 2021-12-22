<?php

if (!function_exists('reseller_comission')) {
function reseller_comission($discount,$percentage=50)
{
    $reseller_profit = round((($percentage/100)*$discount),2);
    return $reseller_profit;
}
}
