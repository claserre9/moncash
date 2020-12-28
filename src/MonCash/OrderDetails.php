<?php


namespace App\MonCash;


use Symfony\Component\HttpClient\Exception\ClientException;

class OrderDetails
{
    public static function RetreiveOrderPayment(Payment $payment, string $orderId)
    {
        $client = $payment->getClient();
        $host = $payment->getAuthenticate()->getHost();
        $uri = "https://$host/v1/RetrieveOrderPayment";
        $access_token = $payment->getAccessToken();
        $token_type = $payment->getTokenType();

        try{
            $response = $client->request('POST', $uri, [
                "json" => [
                    "orderId" => $orderId
                ],
                "headers" => [
                    "accept" => "application/json",
                    "authorization" => "$token_type $access_token",
                    "content-type" => "application/json"
                ]
            ]);
            return $response->toArray();
        }catch (ClientException $exception){
            return $exception->getResponse()->toArray();
        }



    }
}