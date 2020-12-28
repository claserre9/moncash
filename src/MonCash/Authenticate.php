<?php


namespace App\MonCash;


use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Authenticate
{
    private $client_id;
    private $client_secret;
    private $host;
    private $mode;

    private $access_token;
    private $token_type;

    private $client;

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @return mixed
     */
    public function getTokenType()
    {
        return $this->token_type;
    }

    /**
     * @return mixed
     */
    public function getMode(): string
    {
        if ($this->host === Host::TEST) {
            $this->mode = 'sandbox';
        }

        if ($this->host === Host::LIVE) {
            $this->mode = 'live';
        }
        return $this->mode;
    }


    /**
     * Authenticate constructor.
     * @param HttpClientInterface $client
     * @param $client_id
     * @param $client_secret
     * @param string $host
     */
    public function __construct(HttpClientInterface $client, $client_id, $client_secret, $host = Host::TEST)
    {
        $this->client = $client;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->host = $host;

    }

    /**
     * @param mixed $client_id
     * @return Authenticate
     */
    public function setClientId($client_id): Authenticate
    {
        $this->client_id = $client_id;
        return $this;
    }

    /**
     * @param mixed $client_secret
     * @return Authenticate
     */
    public function setClientSecret($client_secret): Authenticate
    {
        $this->client_secret = $client_secret;
        return $this;
    }

    /**
     * @param mixed|string $host
     * @return Authenticate
     */
    public function setHost(string $host): Authenticate
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getHost(): string
    {
        return $this->host;
    }


    /**
     * @return array
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function getResponse(): array
    {
        $uri = "https://$this->client_id:$this->client_secret@$this->host/oauth/token";

        $client = $this->client;
        try {
            $response = $client->request('POST', $uri, [
                'query' => [
                    'scope' => 'read,write',
                    'grant_type' => 'client_credentials'
                ],
                'headers' => [
                    'accept' => 'application/json'
                ]
            ]);
            $arrResp = $response->toArray();
            $this->access_token = $arrResp['access_token'];
            $this->token_type = $arrResp['token_type'];
            return [
                "response" => $arrResp,
                "status" => $response->getStatusCode()
            ];

        } catch (ClientException $exception) {

            return [
                "response" => $exception->getResponse()->toArray(),
                "status" => $exception->getResponse()->getStatusCode()
            ];
        } catch (TransportExceptionInterface $e) {
            return ["response" => $e->getMessage()];
        }
    }


}