<?php

namespace App\Services;
use App\Models\SecretStore;
use Illuminate\Support\Facades\Crypt;

/**
 * Class DtOneProvider
 * @package App\Services
 */
class DtOneProvider
{
    private $access_token;
    public function __construct()
    {
        $token = SecretStore::where('company_name','dtone')->first()->content;
        $this->access_token = Crypt::decrypt($token);
    }

    public function fetch_product($operator_id)
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('https://dvs-api.dtone.com/v1/products?operator_id='.$operator_id.'&type=FIXED_VALUE_RECHARGE&benefit_types=CREDITS',['headers' => [
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

        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('https://dvs-api.dtone.com/v1/lookup/mobile-number/'.$mobile,['headers' => [
            'Authorization'     => 'Basic '.$this->access_token,
            'Accept'=> 'application/json',

            ],'verify' => false]);

            $status = $operator_request->getStatusCode();

            $operator_response = $operator_request->getBody();

            $operator_response = json_decode($operator_response);

            if($status == 200)
            {


            $operator_id = $operator_response[0]->id;
            return ['payload'=>$this->fetch_product($operator_id),'status'=>true];
            }
            else
            {

            return ['payload'=>$operator_response,'status'=>false];
            }

    }

    public function confirmation($txid)
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->post('https://dvs-api.dtone.com/v1/async/transactions/'.$txid.'/confirm',[
            'headers' => [
            'Authorization'=>'Basic '.$this->access_token,
            'Content-Type' => 'application/json'

            ],
            'verify' => false,
            'json' => [
                    'transaction_id' => $txid,


                    ]
        ]);




            $status = $response->getStatusCode();
            $operator_response = $response->getBody();

            $operator_response = json_decode($operator_response);
            if($status == 202)
            {
                return ['status'=>true,'message'=>$operator_response->status->message];
            }
            else
            {
                return ['status'=>false,'message'=>$operator_response];
            }


    }


    public function recharge($sku_id,$txid,$mobile)
    {

        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->post('https://dvs-api.dtone.com/v1/async/transactions',[
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


        $response = json_decode($response->getBody());


        if($status == '200' || $status == '201')
            {

            $confirmation = $this->confirmation($response->id);
            if($confirmation['status']==true)
            {
            return ['payload'=>$response,'status'=>true];
            }
            else{
                return ['payload'=>$confirmation['message'],'status'=>false];
            }
            }
            else
            {

            return ['payload'=>$response,'status'=>false];
            }
    }

}
