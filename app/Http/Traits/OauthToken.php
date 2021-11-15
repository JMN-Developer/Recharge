<?php

namespace App\Http\Traits;
use App\Models\oauth_token;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Auth;

trait OauthToken {
    public function createToken() {
        // Fetch all the students from the 'student' table.
    //    if(Auth::check())
    // {
        
    // }
    $client_id = oauth_token::first()->client_id;
    $client_secret = oauth_token::first()->client_secret;

    $client = new \GuzzleHttp\Client(['http_errors' => false]);
    $recharge_request = $client->post('https://idp.ding.com/connect/token',[
        'headers' => [
          
            'Content-Type' => 'application/x-www-form-urlencoded'
            ],
   
    'verify' => false,
    'form_params' => [
        'client_id' => Crypt::decryptString($client_id),
        'client_secret' => Crypt::decryptString($client_secret),
        'grant_type' => 'client_credentials',
            ]
        ]); 
        $product_responses = $recharge_request->getBody();
        //file_put_contents('test2.txt',$product_responses);

        $prod = json_decode($product_responses,true);
        return $prod['access_token'];

    }
}