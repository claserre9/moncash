<?php

namespace App\Controller;

use App\MonCash\Payment;
use App\MonCash\PaymentDetails;
use App\MonCash\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param HttpClientInterface $client
     * @param SessionInterface $session
     * @return Response
     */
    public function index(HttpClientInterface $client, SessionInterface $session): Response
    {
        $client_id = '7834cbaabb89cf9ce232cd407b19df30';
        $client_secret = 'Dq8SWBS2whE_B_VxjkgdODcJ0ZOx3Ou4gdvj3JhtYOe-Dz9jox4fMgZwYeXCRJBp';
        //$auth = new Authenticate($client,$client_id,$client_secret);
        $session->start();
        $orderId = $session->getId();

        $payment = new Payment($client, $client_id, $client_secret, $orderId, 2000);

        $paymentResponse = $payment->createPayment();
        $session->set("token_type", $payment->getTokenType());
        $session->set("access_token", $payment->getAccessToken());
        $session->set("host", $payment->getHost());

        $paymentinfos = new PaymentDetails($paymentResponse);

        return $this->render('home/index.html.twig', [
            'redirect_url' => $paymentinfos->getRedirectUrl(),
        ]);
    }

    /**
     * @Route("/confirm", name="confirm")
     * @param HttpClientInterface $client
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function confirm(HttpClientInterface $client, Request $request, SessionInterface $session): Response
    {
        $transactionId = $request->query->get("transactionId");
        $token_type = $session->get("token_type");
        $access_token = $session->get("access_token");
        $host = $session->get("host");
        $transactionDetails = Transaction::RetrieveTransactionPayment($client, $host, $access_token, $token_type, $transactionId);
        //dd($transactionDetails['status']);
//        if(200!==$transactionDetails['status']){
//
//        }

        return $this->render("home/confirm.html.twig", $transactionDetails);
    }

    /**
     * @Route(name="backtohome")
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function backToHome(SessionInterface $session): RedirectResponse
    {
        $session->clear();
        return $this->redirectToRoute('home');
    }
}
