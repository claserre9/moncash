<?php


namespace App\MonCash;


use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Payment
{
    /**
     * @Authenticate
     */
    private $authenticate;

    /**
     * @var
     */
    private $orderId;

    /**
     * @float
     */
    private $amout;

    private $client;



    /**
     * Payment constructor.
     * @param $client_id
     * @param $client_secret
     * @param $orderId
     * @param $amout
     */
    public function __construct(HttpClientInterface $client, $client_id, $client_secret, $orderId, $amout)
    {
        $this->authenticate = new Authenticate($client, $client_id, $client_secret);
        $this->orderId = $orderId;
        $this->amout = $amout;
        $this->client = $client;

    }


    /**
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function createPayment(): array
    {
        $client = $this->client;

        $host = $this->authenticate->getHost();

        $response = $this->authenticate->getResponse();
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

        } catch (ClientException $exception) {
            return $exception->getResponse()->toArray();
        }

    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->authenticate->getAccessToken();
    }

    /**
     * @return mixed
     */
    public function getTokenType()
    {
        return $this->authenticate->getTokenType();
    }

    /**
     * @return Authenticate
     */
    public function getAuthenticate(): Authenticate
    {
        return $this->authenticate;
    }

    /**
     * @return HttpClientInterface
     */
    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }


}