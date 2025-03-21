<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Services\CartService;
use Stripe\Checkout\Session;
use App\Services\StripeService;
use Symfony\Component\HttpFoundation\Request;

#[Route('/payment', name: 'app_payment')]

final class PaymentController extends AbstractController
{
    private $stripeService;

    public function __construct(StripeService $stripeService, 
                                protected CartService $cartService)
    {
        $this->stripeService = $stripeService;
    }


    #[Route('/checkout', name: '_checkout')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(OrderRepository $orderRepo): Response
    {
       
        $cart = $this->cartService->getCart();

        $orderID = $orderRepo->findOneBy([], ['id' => 'desc']);

        $checkoutSession = $this->stripeService
                                ->createCheckoutSession($orderID->getId(), $cart,$this->getUser()->getEmail());

        return $this->render('payment/redirect.html.twig', [
            'checkoutUrl' => $checkoutSession->url,
        ]);
    }

    #[Route('/success', name: '_success')]
    public function success(Request $request): Response
    {
     
        $sessionId = $request->query->get('session_id');
    

        if (!$sessionId) {
            throw $this->createNotFoundException('Session ID manquant.');
        }
    
        $session = Session::retrieve($sessionId);
    
        if ($session->payment_status !== 'paid') {
            throw $this->createNotFoundException('Le paiement n’a pas été validé.');
        }
    
        // Logique post-paiement (enregistrement de commande, etc.)
        return $this->render('payment/success.html.twig');


    }
}
