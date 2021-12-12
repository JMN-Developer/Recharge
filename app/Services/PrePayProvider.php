<?php

namespace App\Services;
use App\Models\SecretStore;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Class BasicToken
 * @package App\Services
 */
class PrePayProvider
{
    private $access_token;
    public function __construct()
    {
        $token = SecretStore::where('company_name','Prepay')->first()->content;
        $this->access_token = Crypt::decrypt($token);
    }

    public function lookup($mobile)
    {

        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://www.valuetopup.com/api/v1/catalog/lookup/mobile/'.$mobile,['headers' => [
            'Authorization'     => 'Basic '.$this->access_token,
            'Accept'=> 'application/json',

            ],'verify' => false]);

            $status = $operator_request->getStatusCode();
            $operator_response = $operator_request->getBody();

            $operator_response = json_decode($operator_response);

            if($status == '200')
            {

            return ['payload'=>$operator_response,'status'=>true];
            }
            else
            {

            return ['payload'=>$operator_response,'status'=>false];
            }

    }
    public function operator_logo($productId){

        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://www.valuetopup.com/api/v1/catalog/sku/logos?productId='.$productId,['headers' => [
            'Authorization'     => 'Basic '.$this->access_token,
            'Accept'=> 'application/json',

            ],'verify' => false]);
            $status = $operator_request->getStatusCode();
            $operator_response = $operator_request->getBody();

            $operator_response = json_decode($operator_response);

            if($status == '200')
            {

            return ['payload'=>$operator_response,'status'=>true];
            }
            else
            {

            return ['payload'=>$operator_response,'status'=>false];
            }

    }

    public function balance_info($productId){

        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://www.valuetopup.com/api/v1/account/balance',['headers' => [
            'Authorization'     => 'Basic '.$this->access_token,
            'Accept'=> 'application/json',

            ],'verify' => false]);
            $status = $operator_request->getStatusCode();
            $operator_response = $operator_request->getBody();

            $operator_response = json_decode($operator_response);
            return $operator_response;

            // if($status == '200')
            // {

            // return ['payload'=>$operator_response,'status'=>true];
            // }
            // else
            // {

            // return ['payload'=>$operator_response,'status'=>false];
            // }

    }
    public function recharge($sku_id,$amount,$txid,$mobile)
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
        Log::info($response);

        if($status == '200')
            {

            return ['payload'=>$response,'status'=>true];
            }
            else
            {

            return ['payload'=>$response,'status'=>false];
            }
    }
}
