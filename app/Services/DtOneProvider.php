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
        $token = SecretStore::where('company_name', 'dtone')->first()->content;
        $this->access_token = Crypt::decrypt($token);
        //file_put_contents('test.txt', $this->access_token);
    }

    public function fetch_product($operator_id)
    {
        // file_put_contents('test.txt', $operator_id);
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('https://dvs-api.dtone.com/v1/products?operator_id=' . $operator_id . '&type=FIXED_VALUE_RECHARGE', ['headers' => [
            'Authorization' => 'Basic ' . $this->access_token,
            'Accept' => 'application/json',

        ], 'verify' => false]);

        $status = $operator_request->getStatusCode();
        $operator_response = $operator_request->getBody();
        // file_put_contents('test.txt',$operator_response);
        $operator_response = json_decode($operator_response);
        //file_put_contents('test.txt',sizeof($operator_response));
        return $operator_response;
    }

    public function lookup($mobile)
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('https://dvs-api.dtone.com/v1/lookup/mobile-number/' . $mobile, ['headers' => [
            'Authorization' => 'Basic ' . $this->access_token,
            'Accept' => 'application/json',

        ], 'verify' => false]);

        $status = $operator_request->getStatusCode();

        $operator_response = $operator_request->getBody();

        $operator_response = json_decode($operator_response);

        if ($status == 200) {
            $operator_id = $operator_response[0]->id;
            $this->operator_details($operator_id);
            return ['payload' => $this->fetch_product($operator_id), 'status' => true];
        } else {
            return ['payload' => $operator_response, 'status' => false];
        }
    }

    public function operator_details($operator_id)
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('https://dvs-api.dtone.com/v1/operators/' . $operator_id . '', ['headers' => [
            'Authorization' => 'Basic ' . $this->access_token,
            'Accept' => 'application/json',

        ], 'verify' => false]);

        $status = $operator_request->getStatusCode();
        $operator_response = $operator_request->getBody();
        Log::info($operator_response);
        $operator_response = json_decode($operator_response);
        return $operator_response;
    }

    public function transaction($transaction_id)
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('https://dvs-api.dtone.com/v1/transactions/' . $transaction_id, ['headers' => [
            'Authorization' => 'Basic ' . $this->access_token,
            'Accept' => 'application/json',

        ], 'verify' => false]);

        $status = $operator_request->getStatusCode();

        $operator_response = $operator_request->getBody();
        //file_put_contents('test2.txt',$operator_response);

        $operator_response = json_decode($operator_response);

        if ($status == 200) {
            return ['payload' => $operator_response, 'status' => true];
        } else {
            return ['payload' => $operator_response, 'status' => false];
        }
    }

    public function transaction_list()
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('https://dvs-api.dtone.com/v1/transactions', ['headers' => [
            'Authorization' => 'Basic ' . $this->access_token,
            'Accept' => 'application/json',

        ], 'verify' => false]);

        $status = $operator_request->getStatusCode();
        $operator_response = $operator_request->getBody();
        // file_put_contents('test.txt',$operator_response);
        //return $operator_response;
        $operator_response = json_decode($operator_response);
        return $operator_response;
        //file_put_contents('test.txt','hello');
    }

    public function recharge($sku_id, $txid, $mobile)
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->post('https://dvs-api.dtone.com/v1/async/transactions', [
            'headers' => [
                'Authorization' => 'Basic ' . $this->access_token,
                'Content-Type' => 'application/json',

            ],
            'verify' => false,
            'json' => [
                'product_id' => $sku_id,
                'external_id' => $txid,
                'credit_party_identifier' => ['mobile_number' => $mobile],
                'auto_confirm' => true,

            ],
        ]);
        $status = $response->getStatusCode();
        //file_put_contents('test.txt',$response->getBody());
        $response = json_decode($response->getBody());

        if ($status == '200' || $status == '201') {
            if ($response->status->message == 'CONFIRMED') {
                sleep(3);
                $transaction_report = $this->transaction($response->id);
                if ($transaction_report['status'] == true) {
                    if ($transaction_report['payload']->status->class->message == 'COMPLETED' || $transaction_report['payload']->status->class->message == 'SUBMITTED') {
                        return ['payload' => $transaction_report['payload'], 'status' => true];
                    } else {
                        return ['message' => $transaction_report['payload']->status->message, 'status' => false];
                    }
                } else {
                    return ['message' => $transaction_report['payload']->status->message, 'status' => false];
                }
            }

            // file_put_contents('test.txt',json_encode($response));
            //     $confirmation = $this->confirmation($response->id);

            // //   file_put_contents('test.txt',json_encode($response));
            //     if($confirmation['status']==true)
            //     {
            //        $transaction_report = $this->transaction($response->id);
            //        if($transaction_report['status']==true)
            //       {
            //         return ['payload'=>$transaction_report[],'status'=>false];
            //       }
            //       else
            //       {
            //         return ['payload'=>$response,'status'=>false];
            //       }

            //     }
            //     else{
            //         return ['payload'=>$confirmation['message'],'status'=>false];
            //     }
        } else {
            return ['message' => 'Some Error Occured. Please try again after sometimes', 'status' => false];
        }
    }

    public function confirmation($txid)
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->post('https://dvs-api.dtone.com/v1/async/transactions/' . $txid . '/confirm', [
            'headers' => [
                'Authorization' => 'Basic ' . $this->access_token,
                'Content-Type' => 'application/json',

            ],
            'verify' => false,
            'json' => [
                'transaction_id' => $txid,
            ],
        ]);

        $status = $response->getStatusCode();
        $operator_response = $response->getBody();

        $operator_response = json_decode($operator_response);
        if ($status == 202) {
            return ['status' => true, 'message' => $operator_response->status->message];
        } else {
            return ['status' => false, 'message' => $operator_response];
        }
    }
}
