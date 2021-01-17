<?php


namespace App\MonCash;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Payment
{
    /**
     * @Credential
     */
    private $credential;

    /**
     * @string
     */
    private $orderId;

    /**
     * @float
     */
    private $amout;

    /**
     * @var HttpClientInterface
     */
    private $client;


    /**
     * Payment constructor.
     * @param HttpClientInterface $client
     * @param $client_id
     * @param $client_secret
     * @param $orderId
     * @param $amout
     */
    public function __construct(HttpClientInterface $client, $client_id, $client_secret, $orderId, $amout)
    {
        $this->credential = new Credential($client, $client_id, $client_secret);
        $this->orderId = $orderId;
        $this->amout = $amout;
        $this->client = $client;

    }


    /**
     * @return array
     */
    public function createPayment(): array
    {
        $client = $this->client;

        $host = $this->credential->getHost();

        $response = $this->credential->getResponse();
        $token_type = $response['response']['token_type'];
        $access_token = $response['response']['access_token'];


        $uri = "https://$host/v1/CreatePayment";

        try {
            $response = $client->request('POST', $uri, [
                "json" => [
                    "amount" => $this->amout,
                    "orderId" => $this->orderId
                ],
                "headers" => [
                    "accept" => "application/json",
                    "authorization" => "$token_type $access_token",
                    "content-type" => "application/json"
                ]
            ]);
            return $response->toArray();

        } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return [
                "response" => $e->getMessage()
            ];
        }

    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->credential->getAccessToken();
    }

    /**
     * @return mixed
     */
    public function getTokenType()
    {
        return $this->credential->getTokenType();
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->credential->getHost();
    }

}