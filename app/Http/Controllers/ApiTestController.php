<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\OauthToken;
use App\Services\SecretProvider;
class ApiTestController extends Controller
{
    //
    use OauthToken;
    protected $epay;

    public function __construct()
    {

        $this->epay = SecretProvider::get_secret('epay');
    }

    public function epay_transaction_list()
    {
        $xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>
        <REQUEST TYPE="TXLIST">
        <USERNAME>UPLIVE_AMICIBIGIOTTERIA</USERNAME>
        <PASSWORD>'.$this->epay.'</PASSWORD>
        <LOCALDATETIME>2008-03-17 09:08:51</LOCALDATETIME>
        <LISTOPTIONS>
        <FROM>2022-03-01 10:00:00</FROM>
        <UNTIL>2008-03-08 11:00:00</UNTIL>
        </LISTOPTIONS>
        </REQUEST>';
        $client = new \GuzzleHttp\Client();
        $recharge_request = $client->post('https://precision.epayworldwide.com/up-interface',[
            'headers' => [
            'api_key'     => 'Etmo8i5V9q862PHn5dNJSb',
            'content_type' => 'application/xml'
            ],
            'verify' => false,
            'body' => $xml
        ]);

        $body = $recharge_request->getBody();
        $xml = simplexml_load_string($body);

        dd($xml);
    }

    public function test_token()
    {
        return $this->createToken();
    }

    public function get_balance()
    {
        $client = new \GuzzleHttp\Client();
        $product_request = $client->get('https://api.dingconnect.com/api/V1/GetBalance',['headers' => [
            'Authrization'     => 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjNBRDlFQjE2OEE3MzhGMTRDQ0M4OEE4NjIyQjczQUE1MzZDOTM5Q0FSUzI1NiIsInR5cCI6ImF0K2p3dCIsIng1dCI6Ik90bnJGb3B6anhUTXlJcUdJcmM2cFRiSk9jbyJ9.eyJuYmYiOjE2MzY4OTcyMTIsImV4cCI6MTYzNjg5OTAxMiwiaXNzIjoiaHR0cHM6Ly9pZHAuZGluZy5jb20iLCJhdWQiOiJkaW5nY29ubmVjdGFwaSIsImNsaWVudF9pZCI6ImZiZWMxZDdiLTE3OWMtNGZhNS1hZTdhLTE5NmRmM2EzMjQxMiIsImp0aSI6IkVFRjU3QjVEMjdDRUNGQTgxMzc1NkQzRkM5MUZEMTgzIiwiaWF0IjoxNjM2ODk3MjEyLCJzY29wZSI6WyJ0b3B1cGFwaSJdfQ.E0WvryAwcp93SGHIcFuWKcOeTBsNo7NcMVA9NIO39ERtbkPlL86AeNBYdE5qw_tKSlMnBBFrCVDAGAJqp66AggQhDM_csOhd1kZ5OILgICjbWNLjRWk_3kZNTRw_aJMr2IoqSORqeNk-lfBqV4RuPJw410JtJMVuFm5_LeTyjFwVsOOPiCTiVek9Ze8xLqhnwW9pfCtl6YGXkaMPJ3L0FOllEj3DZ9bxZGD9GV111Qv3lGpPmsXBwN0DmzIOjpXeXj_dfflrh14N9JdJvEnRjwvrYq4t84wqI0eSrPAvNdOM59CZ5FeIBSJAWwRduBWJNAXxpLtRV3A8CDc2ysEREN1kX-OEOIYh3osxue4uSP3Pl_GEHRGvQ27Zm1jJgoJ9T8IMLIqFIjCWaDsoWGVnNjKIhx6-r4TsanNNTcSmyPIRrHo-QE0F1oZmvlppSjCVu6pOHAPLJmTYss-FaHGI7WZ3hevkGnilN5lojPzZsdpilg7DQ7j56c2vFSwVyxjiySz3VpJwEsLQX7sdAP2SM21jIkmeFNYCmAxaYohs2Tk5EXl5rVWf0zjOPK-GUjTL7gsAl772wFHvI_Iag72dlz094_jWe3rPiHdciGWGLN6sVtyDQT4xPw5Tn6NkuLVs4AEMeKHYkMqpP-RoyRw9Gyo_vKfpTq6Tp50OnDXkOs4'
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


