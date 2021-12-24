<?php

namespace App\Services;

/**
 * Class DtOneProvider
 * @package App\Services
 */
class DtOneProvider
{
    private $access_token;
    public function __construct()
    {
        //$token = SecretStore::where('company_name','Prepay')->first()->content;
        $this->access_token = 'MDAzYzA0OWQtMWFhMy00ZjZlLTg4NjMtZGI2NTZlNmY2Njk2OmUwYjRjZDkzLTYxYWEtNDI2Yy04OTY1LTQ0OTgyZjUzOTYwYQ==';
    }

    public function fetch_product($operator_id)
    {
        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://preprod-dvs-api.dtone.com/v1/products?operator_id='.$operator_id.'&type=FIXED_VALUE_RECHARGE&benefit_types=CREDITS',['headers' => [
            'Authorization'     => 'Basic '.$this->access_token,
            'Accept'=> 'application/json',

            ],'verify' => false]);

            $status = $operator_request->getStatusCode();
            $operator_response = $operator_request->getBody();

            $operator_response = json_decode($operator_response);

            return $operator_response;
    }

    public function lookup($mobile)
    {

        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://preprod-dvs-api.dtone.com/v1/lookup/mobile-number/'.$mobile,['headers' => [
            'Authorization'     => 'Basic '.$this->access_token,
            'Accept'=> 'application/json',

            ],'verify' => false]);

            $status = $operator_request->getStatusCode();
            $operator_response = $operator_request->getBody();

            $operator_response = json_decode($operator_response);

            if($status == '200')
            {


            $operator_id = $operator_response[0]->id;
            return ['payload'=>$this->fetch_product($operator_id),'status'=>true];
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
    public function recharge($sku_id,$txid,$mobile)
    {

        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->post('https://preprod-dvs-api.dtone.com/v1/async/transactions',[
            'headers' => [
            'Authorization'=>'Basic '.$this->access_token,
            'Content-Type' => 'application/json'

            ],
            'verify' => false,
            'json' => [
                    'product_id' => $sku_id,
                    'external_id'=>$txid,
                    'credit_party_identifier'=>['mobile_number'=>$mobile]

                    ]
        ]);
        $status = $response->getStatusCode();
        file_put_contents('test.txt',$response->getBody());
        $response = json_decode($response->getBody());


        if($status == '200')
            {

            return ['payload'=>$response,'status'=>true];
            }
            else
            {

            return ['payload'=>$response,'status'=>false];
            }
    }
    public function pin($sku_id,$txid){
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->post('https://www.valuetopup.com/api/v1/transaction/pin',[
            'headers' => [
            'Authorization'=>'Basic '.$this->access_token,
            'Content-Type' => 'application/json'

            ],
            'verify' => false,
            'json' => [
                    'skuId' => $sku_id,
                    'correlationId'=>$txid,


                    ]
        ]);

        $status = $response->getStatusCode();
        $response = json_decode($response->getBody());


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
