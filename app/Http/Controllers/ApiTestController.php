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
        return $prod;

    }

    public function check_operator(Request $request)
    {
        $number = $request->number;
        $client = new \GuzzleHttp\Client();
        $operator_request = $client->get('https://api.dingconnect.com/api/V1/GetProviders?accountNumber='.$number,['headers' => [
            'api_key'     => 'G4ymoFlN97B6PhZgK1yzuY'
            ],'verify' => false]);
        $operator_response = $operator_request->getBody();
        //$data = json_decode($operator_response,true);
        return $operator_response;
    }

    public function get_products(Request $request)
    {
        $code = $request->code;
        $client = new \GuzzleHttp\Client();
        $product_request = $client->get('https://api.dingconnect.com/api/V1/GetProducts?&providerCodes='.$code,['headers' => [
            'api_key'     => 'G4ymoFlN97B6PhZgK1yzuY'
            ],'verify' => false]);
        $product_responses = $product_request->getBody();
        return $product_responses;
    }
}


