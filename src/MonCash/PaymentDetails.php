<?php


namespace App\MonCash;




class PaymentDetails
{
    private $paymentCreateResponse = [];

    private $mode;

    private $paymentToken;

    private $status;

    private $redirectUrl;

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        if($this->mode === "sandbox"){
            $gatewayBaseUrl = GatewayBaseUrl::TEST;
            $this->redirectUrl = "$gatewayBaseUrl/Payment/Redirect?token=$this->paymentToken";
        }
        if($this->mode === "live"){
            $gatewayBaseUrl = GatewayBaseUrl::LIVE;
            $this->redirectUrl = "$gatewayBaseUrl/Payment/Redirect?token=$this->paymentToken";
        }

        return $this->redirectUrl;
    }

    private $created;

    private $expired;

    /**
     * PaymentDetails constructor.
     * @param array $paymentCreateResponse
     */
    public function __construct(array $paymentCreateResponse)
    {
        $this->paymentCreateResponse = $paymentCreateResponse;

        $this->mode =$paymentCreateResponse['mode'];
        $this->status =$paymentCreateResponse['status'];
        $this->expired = $paymentCreateResponse['payment_token']['expired'];
        $this->created = $paymentCreateResponse['payment_token']['created'];
        $this->paymentToken = $paymentCreateResponse['payment_token']['token'];


    }

}