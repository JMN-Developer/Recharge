<?php

namespace App\Services;

/**
 * Class GenerateTransactionId
 * @package App\Services
 */
class GenerateTransactionId
{
    private $reseller_id,$service_code;

    public function __construct($reseller_id,$service_code)
    {
        if($reseller_id<10)
        {
            $reseller_id = '0'.$reseller_id;
        }
        $this->reseller_id = $reseller_id;
        $this->service_code = $service_code;
    }
    public function transaction_id()
    {

        $transaction_id = date('dmYHis').$this->reseller_id.$this->service_code;
        return $transaction_id;
    }
}
