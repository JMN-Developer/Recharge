<?php

namespace App\Services;
use App\Models\SecretStore;
use Illuminate\Support\Facades\Crypt;


/**
 * Class BasicToken
 * @package App\Services
 */
class PrePayProvider
{
    private $access_token;
    public function __construct()
    {
        $token = SecretStore::where('company_name','prepay')->first()->content;
        $this->access_token = Crypt::decrypt($token);
    }

    public function lookup($mobile)
    {
        $mobile = '39'.$mobile;
        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://www.valuetopup.com/api/v1/catalog/lookup/mobile/'.$mobile,['headers' => [
            'Authorization'     => 'Basic '.$this->access_token,
            'Accept'=> 'application/json',

            ],'verify' => false]);


        $operator_response = $operator_request->getBody();
       // file_put_contents('test.txt',$operator_response);
        $operator_response = json_decode($operator_response);
        return $operator_response;

    }
    public function topup($sku_id,$amount,$txid,$mobile)
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->post('https://www.valuetopup.com/api/v1/transaction/topup',[
            'headers' => [
            'Authorization'=>'Basic '.$this->access_token,
            'Content-Type' => 'application/json'

            ],
            'verify' => false,
            'json' => [
                    'skuId' => $sku_id,
                    'amount' => $amount,
                    'correlationId'=>$txid,
                    'mobile'=>$mobile,


                    ]
        ]);
        $status = $response->getStatusCode();
        $response = $response->getBody();
    }
}
