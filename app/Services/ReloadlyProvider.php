<?php

namespace App\Services;
use App\Models\SecretStore;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
/**
 * Class ReloadlyTokenProvider
 * @package App\Services
 */
class ReloadlyProvider
{

    public function access_token()
    {
        $last_updated_date = SecretStore::where('company_name','Reloadly')->where('secret_type','access_token')->first();
        $startTime = Carbon::parse($last_updated_date->updated_at);
        $endTime = Carbon::parse(Carbon::now()->toDateTimeString());
        $totalDuration = $endTime->diffInSeconds($startTime);
        //file_put_contents('test.txt',$totalDuration." ".$last_updated_date->content);
        if($totalDuration<5184000)
        {
           return Crypt::decrypt($last_updated_date->content);
        }

        $client_id = Crypt::decrypt(SecretStore::where('company_name','Reloadly')->where('secret_type','client_id')->first()->content);
        $client_secret = Crypt::decrypt(SecretStore::where('company_name','Reloadly')->where('secret_type','client_secret')->first()->content);
       // $client_id = 'SHILVCMRGJab2DfLIxhaKqCNxlgoLFvv';
        //$client_secret = 'ga0OsMAdey-rg7uULTs52Bqv5B3VJz-a8zdfbUn2eOxR92gKED18Udk4z8xkJkK';
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->post('https://auth.reloadly.com/oauth/token',[
            'headers' => [
            'Content-Type' => 'application/json'
            ],
            'verify' => false,
            'json' => [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'grant_type' => 'client_credentials',
                    'audience' => 'https://topups.reloadly.com',

                    ]
        ]);
        $response = $response->getBody();
        //file_put_contents('test.txt',$operator_response);
        $data = json_decode($response);


        SecretStore::where('company_name','Reloadly')->where('secret_type','access_token')->update(['content'=>Crypt::encrypt($data->access_token)]);


        return $data->access_token;

    }
    public function operator_details($mobile,$iso)
    {
        //file_put_contents('test.txt',$this->access_token());
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('https://topups.reloadly.com/operators/auto-detect/phone/'.$mobile.'/countries/'.$iso.'?suggestedAmountsMap=true&SuggestedAmounts=true',['headers' => [
            'Authorization'     => 'Bearer '.$this->access_token(),
            'Accept'=> 'application/com.reloadly.topups-v1+json',

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

    public function recharge($operator_id,$amount,$country_code,$number,$txid)
    {
        //$amount = $amount+0.01;
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->post('https://topups.reloadly.com/topups',[
            'headers' => [
            'Authorization'=>'Bearer '.$this->access_token(),
            'Content-Type' => 'application/json',
            'Accept'=> 'application/com.reloadly.topups-v1+json'

            ],
            'verify' => false,
            'json' => [
                    'operatorId' => $operator_id,
                    'amount' => $amount,
                    'useLocalAmount' => false,
                    'customIdentifier'=>$txid,
                    'recipientPhone'=>['countryCode'=>$country_code,'number'=>$number]

                    ]
        ]);
        $status = $response->getStatusCode();
        $response = $response->getBody();
        //file_put_contents('test.txt',$response);
        if($status == '200')
        {
        $data = json_decode($response);

        return ['payload'=>$data,'status'=>true];
        }
        else
        {
            $data = json_decode($response);
            return ['payload'=>$data,'status'=>false];
        }
        //return $data;

    }
}
