<?php

namespace App\Services;

use GuzzleHttp\Client;
use Log;

/**
 * Class DtOneProvider
 * @package App\Services
 */
class Flixbus
{
    private $api_authentication;
    private $api_session;
    private $base_url;
    public $responseData;

    public function __construct()
    {
        $this->base_url = 'https://global.api-dev.flixbus.com';
        $this->api_authentication = 'DEV_TEST_TOKEN_STAGING';
        $this->api_session = $this->fetchSessionToken();
    }

    public function fetchSessionToken()
    {
        $client = new Client();

        $response = $client->request('POST', $this->base_url . '/public/v1/partner/authenticate.json', [
            'headers' => [
                'Accept-Language' => 'en',
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'X-API-Authentication' => $this->api_authentication,
            ],
            'form_params' => [
                'email' => 'DEV_TEST_STAGING@mail.com',
                'password' => 'DEV_TEST_STAGING',
            ],
        ]);

        $statusCode = $response->getStatusCode();
        $responseData = json_decode($response->getBody(), true);
        return $responseData['token'];

    }

    //write search function with search.json api
    public function searchTrips($search_by, $from, $to, $departure_date, $adult, $children, $bikes)
    {

        $client = new Client();

        $url = $this->base_url . '/public/v1/trip/search.json';
        $queryParams = [
            'search_by' => $search_by,
            'from' => $from,
            'to' => $to,
            'departure_date' => $departure_date,
            'adult' => $adult,
            'children' => $children,
            'bikes' => $bikes,
        ];

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Accept-Language' => 'en',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-API-Authentication' => $this->api_authentication,
                ],
                'query' => $queryParams,
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $responseData = json_decode($response->getBody()->getContents(), true);
                Log::error('Request failed', ['status' => $response->getStatusCode(), 'response' => $responseData]);
            } else {
                Log::error('Request failed with no response', ['exception' => $e]);
            }
            throw $e; // Optionally re-throw the exception if you want it to bubble up.
        }

        return $responseData;

    }

    public function updateReservationItems($trip_uid, $adult, $children = 0, $currency, $passengers, $email)
    {

        $client = new Client();

        $url = $this->base_url . '/public/v1/reservation/items.json';

        $formParams = [
            'trip_uid' => $trip_uid,
            'adult' => $adult,
            'currency' => $currency,
        ];

        try {
            $response = $client->request('PUT', $url, [
                'headers' => [
                    'Accept-Language' => 'en',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'X-API-Authentication' => $this->api_authentication,
                    'X-API-Session' => $this->api_session,
                    'User-Agent' => 'JM Nation',
                ],
                'form_params' => $formParams,
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);
            $reservation_id = $responseData['reservation']['id'];
            $reservation_token = $responseData['reservation']['token'];
            $result = $this->getReservationPassengers($reservation_id, $reservation_token, $passengers, $email);
            //return $result;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody();
            $errorDetails = json_decode($responseBody->getContents(), true);
            Log::error("API request failed", ['details' => $errorDetails]);
            return $errorDetails; // return error details or consider throwing an exception here
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw $e; // re-throw the exception to be handled by global exception handler
        }
    }
    public function getCities()
    {
        $client = new Client();

        $url = $this->base_url . '/public/v1/network.json';
        $query = [
            'name' => 'New',
            'countryName' => 'Italy',
        ];
        $response = $client->request('GET', $url, [
            'headers' => [
                'Accept-Language' => 'en',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Authentication' => $this->api_authentication,
            ],
            'query' => $query,
        ]);

        $statusCode = $response->getStatusCode();
        $responseData = json_decode($response->getBody(), true);

        return $responseData;
    }

    public function startPayment($reservation, $reservation_token, $email)
    {
        $client = new Client();

        $url = $this->base_url . '/public/v1/payment/start.json';

        $formParams = [
            'reservation' => $reservation,
            'reservation_token' => $reservation_token,
            'email' => $email,
            'payment' => [
                'psp' => 'offline',
                'method' => 'cash',
            ],
        ];

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Accept-Language' => 'en',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'X-API-Authentication' => $this->api_authentication,
                    'X-API-Session' => $this->api_session,
                ],
                'form_params' => $formParams,
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);
            Log::info($responseData);
            if ($responseData['result'] == true) {
                $result = $this->commitPayment($reservation, $reservation_token, $responseData['payment_id']);
                //return $result;
            }

            //Log::info($responseData);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody();
            $errorDetails = json_decode($responseBody->getContents(), true);
            Log::error("API request failed", ['details' => $errorDetails]);
            return $errorDetails; // return error details or consider throwing an exception here
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw $e; // re-throw the exception to be handled by global exception handler
        }
    }

    public function addPassanger($reservationId, $reservationToken, $passengers, $email)
    {

        $client = new Client();

        $url = "https://global.api-dev.flixbus.com/public/v1/reservations/{$reservationId}/passengers.json";

        $formParams = [
            'reservation_token' => $reservationToken,
            'with_donation' => true,
            'donation_partner' => 'atmosfair',
            'passengers' => $passengers,
        ];

        try {
            $response = $client->request('PUT', $url, [
                'headers' => [
                    'Accept-Language' => 'en',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'X-API-Authentication' => $this->api_authentication,
                ],
                'form_params' => $formParams,
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

            if ($responseData['result'] == true) {
                $result = $this->startPayment($reservationId, $reservationToken, $email);
                //return $result;
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody();
            $errorDetails = json_decode($responseBody->getContents(), true);
            Log::error("API request failed", ['details' => $errorDetails]);
            return $errorDetails; // return error details or consider throwing an exception here
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw $e; // re-throw the exception to be handled by global exception handler
        }
    }

    public function getReservationPassengers($reservation_id, $reservation_token, $passengers, $email)
    {

        $client = new Client();

        $url = $this->base_url . '/public/v1/reservations/' . $reservation_id . '/passengers.json';

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Accept-Language' => 'en',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-API-Authentication' => $this->api_authentication,
                ],
                'query' => [
                    'reservation_token' => $reservation_token,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

            foreach ($responseData['trips'] as $trip) {
                foreach ($trip['passengers'] as $index => $passenger) {
                    if (isset($passengers[$index])) { // check if the passenger exists in the request data
                        $passengers[$index]['reference_id'] = $passenger['reference_id']; // add the reference_id
                    }
                }
            }
            $result = $this->addPassanger($reservation_id, $reservation_token, $passengers, $email);
            //return $result;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody();
            $errorDetails = json_decode($responseBody->getContents(), true);
            Log::error("API request failed", ['details' => $errorDetails]);
            return $errorDetails; // return error details or consider throwing an exception here
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw $e; // re-throw the exception to be handled by global exception handler
        }
    }

    public function commitPayment($reservation, $reservation_token, $payment_id)
    {
        $client = new Client();

        $url = $this->base_url . '/public/v1/payment/commit.json';

        try {
            $response = $client->request('PUT', $url, [
                'headers' => [
                    'Accept-Language' => 'en',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'X-API-Authentication' => $this->api_authentication,
                    'X-API-Session' => $this->api_session,
                ],
                'form_params' => [
                    'reservation' => $reservation,
                    'reservation_token' => $reservation_token,
                    'payment_id' => $payment_id,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);
            if ($responseData['result'] == true) {
                $result = $this->getOrderInfo($responseData['order_id'], $responseData['download_hash']);
                //return $result;
            }

            //return $responseData;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody();
            $errorDetails = json_decode($responseBody->getContents(), true);
            Log::error("API request failed", ['details' => $errorDetails]);
            return $errorDetails; // return error details or consider throwing an exception here
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw $e; // re-throw the exception to be handled by global exception handler
        }
    }
    public function getOrderInfo($id, $download_hash)
    {
        $client = new Client();

        $url = $this->base_url . '/public/v2/orders/' . $id . '/info.json';

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Accept-Language' => 'en',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-API-Authentication' => $this->api_authentication,
                ],
                'query' => [
                    'download_hash' => $download_hash,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $this->responseData = json_decode($response->getBody(), true);

            Log::info($this->responseData);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody();
            $errorDetails = json_decode($responseBody->getContents(), true);
            Log::error("API request failed", ['details' => $errorDetails]);
            return $errorDetails; // return error details or consider throwing an exception here
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw $e; // re-throw the exception to be handled by global exception handler
        }
    }

}
