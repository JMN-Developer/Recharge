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
        $this->reseller_id = str_pad($reseller_id, 4, "0", STR_PAD_LEFT);
        $this->service_code = $service_code;
    }
    public function transaction_id()
    {

        $transaction_id = date('dmYHis').$this->reseller_id.$this->service_code;
        return $transaction_id;
    }
}
