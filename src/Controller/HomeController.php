<?php

namespace App\Controller;

use App\MonCash\Authenticate;
use App\MonCash\Payment;
use App\MonCash\PaymentDetails;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(HttpClientInterface $client): Response
    {
        $client_id = '7834cbaabb89cf9ce232cd407b19df30';
        $client_secret = 'Dq8SWBS2whE_B_VxjkgdODcJ0ZOx3Ou4gdvj3JhtYOe-Dz9jox4fMgZwYeXCRJBp';
        //$auth = new Authenticate($client,$client_id,$client_secret);

        $payment = new Payment($client,$client_id,$client_secret,"djnas1562aas", 10000);
        $paymentResponse = $payment->createPayment();


        $paymentinfos = new PaymentDetails($paymentResponse);

        return $this->render('home/index.html.twig', [
            'redirect_url' => $paymentinfos->getRedirectUrl(),
        ]);
    }
}
