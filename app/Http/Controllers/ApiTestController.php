<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiTestController extends Controller
{
    //
    public function get_balance()
    {
        $client = new \GuzzleHttp\Client();
        $product_request = $client->get('https://api.dingconnect.com/api/V1/GetBalance',['headers' => [
            'api_key'     => 'G4ymoFlN97B6PhZgK1yzuY'
            ],'verify' => false]);
        $product_responses = $product_request->getBody();

        $prod = json_decode($product_responses,true);

        $bal = $prod['Balance'];

    }
}
