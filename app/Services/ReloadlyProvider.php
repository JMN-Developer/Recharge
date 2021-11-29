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
    // private $client_id,$client_secret;

    // public function __construct($client_id,$client_secret)
    // {

    //     $this->client_id = $client_id;
    //     $this->client_secret = $client_secret;
    // }

    public function access_token()
    {
        $last_updated_date = SecretStore::where('company_name','Reloadly')->where('secret_type','access_token')->first();
        $startTime = Carbon::parse($last_updated_date->updated_at);
        $endTime = Carbon::parse(Carbon::now()->toDateTimeString());
        $totalDuration = $endTime->diffInSeconds($startTime);
        file_put_contents('test.txt',$totalDuration." ".$last_updated_date->content);
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
                    'audience' => 'https://topups-sandbox.reloadly.com',

                    ]
        ]);
        $response = $response->getBody();
        $data = json_decode($response);


        SecretStore::where('company_name','Reloadly')->where('secret_type','access_token')->update(['content'=>Crypt::encrypt($data->access_token)]);


        return $data->access_token;

    }
    public function operator_details($mobile,$iso)
    {
        //file_put_contents('test.txt',$this->access_token());
        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://topups-sandbox.reloadly.com/operators/auto-detect/phone/'.$mobile.'/countries/'.$iso.'?suggestedAmountsMap=true&SuggestedAmounts=true',['headers' => [
            'Authorization'     => 'Bearer '.$this->access_token(),
            'Accept'=> 'application/com.reloadly.topups-v1+json'
            ],'verify' => false]);
        $operator_response = $operator_request->getBody();
        $operator_response = json_decode($operator_response);
        return $operator_response;
    }
}
