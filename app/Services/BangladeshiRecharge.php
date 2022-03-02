<?php

namespace App\Services;
use SoapClient;
use Auth;
use App\Services\GenerateTransactionId;
use App\Models\currency_rate;
use SebastianBergmann\Environment\Console;
use Illuminate\Support\Facades\Crypt;
use App\Models\SecretStore;

/**
 * Class BangladeshiRecharge
 * @package App\Services
 */
class BangladeshiRecharge
{
    private $client_id = 'JMNationAPI';
    private $client_pass;
    private $wsdl_path = "http://vrapi.sslwireless.com/?wsdl";
    private $soap_exception_occured;
    private $exception;
    private $client;

    public function __construct()
    {
        $token = SecretStore::where('company_name','SSL')->first()->content;
        $this->client_pass = Crypt::decrypt($token);
        // $options = [
        //     'cache_wsdl'     => WSDL_CACHE_NONE,
        //     'trace'          => 1,
        // ];
        try {
            $this->client = new SoapClient($this->wsdl_path);
            $this->soap_exception_occured = false;
            $this->exception = '';
            } catch(SoapFault $exception) {
            $this->soap_exception_occured = true;
            $this->exception = 'Soap Exception '.$exception;
            }



    }
    public function current_euro_rate()
    {
        $date = date('Y-m-d');
        $currency_rate = currency_rate::where('updated_at','LIKE',$date.'%')->first();
        if($currency_rate)
        {
        return $currency_rate->eur;
        }
        else
        {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('https://freecurrencyapi.net/api/v2/latest?apikey=7db968c0-956a-11ec-b831-1b1d53e27789&base_currency=EUR');
        $response = $operator_request->getBody();
        $response = json_decode($response);
        currency_rate::where('id',1)->update(['eur'=>$response->data->BDT]);
        return $response->data->BDT;
        }

    }


    public function offer_details($operator_id)
    {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $operator_request = $client->get('http://vrapi.sslwireless.com/rest/specialAmount/v2/?client_id=JMNationAPI&connection_type=prepaid&lang=en&operator_id='.$operator_id);

           // $status = $operator_request->getStatusCode();

            $response = $operator_request->getBody();
            //file_put_contents('test.txt',$response);

            $response = json_decode($response);
            //file_put_contents('test.txt',json_encode($response->data->triggerAmount->list));
            return $response->data->triggerAmount->list;

    }
    public function CreateRecharge($guid,$operator_id,$recipient_msisdn,$amount)
    {
        $amount = ceil($amount);
        //file_put_contents('test.txt',$amount);
        try {
            $data = $this->client->CreateRecharge($this->client_id,$this->client_pass,$guid,$operator_id,$recipient_msisdn,$amount,'prepaid');
            //file_put_contents('test.txt',json_encode($data));
            } catch(SoapFault $exception) {
                $this->soap_exception_occured = true;
                $this->exception = 'Soap Exception '.$exception;
            }
            //file_put_contents('create_recharge.txt',json_encode($data));
            return ['data'=>$data,'soap_exception_occured'=>$this->soap_exception_occured,'exception'=>$this->exception];
    }
    public function InitRecharge($guid,$vr_guid)
    {
        try {
            $data = $this->client->InitRecharge($this->client_id,$this->client_pass,$guid,$vr_guid);
            //file_put_contents('test.txt',json_encode($data));
            } catch(SoapFault $exception) {
                $this->soap_exception_occured = true;
                $this->exception = 'Soap Exception '.$exception;
            }
            //file_put_contents('init_recharge.txt',json_encode($data));
            return ['data'=>$data,'soap_exception_occured'=>$this->soap_exception_occured,'exception'=>$this->exception];
    }

    public function balanceInfo()
    {
        try {
            $data = $this->client->GetBalanceInfo($this->client_id);
            } catch(SoapFault $exception) {
                $this->soap_exception_occured = true;
                $this->exception = 'Soap Exception '.$exception;
            }

            return ['balance_info'=>$data->available_credit,'soap_exception_occured'=>$this->soap_exception_occured,'exception'=>$this->exception];
    }

    public function operatorInfo($msisdn)
    {
       $verify =  $this->verifyMsisdn($msisdn);
       if($verify['data']==0)
       {
        return ['exception'=>'Phone number is not valid','soap_exception_occured'=>true];
       }

        try {
            $data = $this->client->FindOperatorInfo($this->client_id,$msisdn);
            } catch(SoapFault $exception) {
                $this->soap_exception_occured = true;
                $this->exception = 'Soap Exception '.$exception;
            }

            return ['data'=>$data,'soap_exception_occured'=>$this->soap_exception_occured,'exception'=>$this->exception];
    }

    public function verifyMsisdn($msisdn)
    {
        try {
            $data = $this->client->VerifyMSISDN($this->client_id,$msisdn);
            } catch(SoapFault $exception) {
                $this->soap_exception_occured = true;
                $this->exception = 'Soap Exception '.$exception;
            }

            return ['data'=>$data,'soap_exception_occured'=>$this->soap_exception_occured,'exception'=>$this->exception];
    }

    public function getOperatorLimit($telco_id)
    {
        try {
            $data = $this->client->GetOperatorLimits($this->client_id,$telco_id);
            } catch(SoapFault $exception) {
                $this->soap_exception_occured = true;
                $this->exception = 'Soap Exception '.$exception;
            }

            return ['data'=>$data,'soap_exception_occured'=>$this->soap_exception_occured,'exception'=>$this->exception];
    }




}
