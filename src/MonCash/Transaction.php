<?php


namespace App\MonCash;


use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Transaction
{
    public static function RetrieveTransactionPayment(HttpClientInterface $client, $host, $access_token, $token_type, string $transactionId): array
    {


        $uri = "https://$host/v1/RetrieveTransactionPayment";

        try {
            $response = $client->request('POST', $uri, [
                "json" => [
                    "transactionId" => $transactionId
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
                "response" => $e->getMessage(),
                "status" => $e->getCode(),
            ];
        }


    }
}