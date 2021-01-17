<?php


namespace App\MonCash;


use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Credential
{
    /**
     * @var string
     */
    private $client_id;

    /**
     * @var string
     */
    private $client_secret;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var string
     */
    private $access_token;

    /**
     * @var string
     */
    private $token_type;

    /**
     * @var HttpClientInterface
     */
    private $client;


    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }


    /**
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->token_type;
    }


    /**
     * @return string
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
    public function __construct(HttpClientInterface $client, string $client_id, string $client_secret, string $host = Host::TEST)
    {
        $this->client = $client;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->host = $host;

    }


    /**
     * @param $client_id
     * @return $this
     */
    public function setClientId($client_id): Credential
    {
        $this->client_id = $client_id;
        return $this;
    }


    /**
     * @param $client_secret
     * @return $this
     */
    public function setClientSecret($client_secret): Credential
    {
        $this->client_secret = $client_secret;
        return $this;
    }


    /**
     * @param string $host
     * @return $this
     */
    public function setHost(string $host): Credential
    {
        $this->host = $host;
        return $this;
    }


    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }


    /**
     * @return array
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

        } catch (TransportExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            return [
                "response" => $e->getMessage()
            ];
        }
    }

}