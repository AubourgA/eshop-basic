<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ShippingMethodRepository;
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


    #[Route('/checkout/{id}', name: '_checkout')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(Order $order, 
                         OrderRepository $orderRepo, 
                         ShippingMethodRepository $shippingMethod): Response
    {
       
        $cart = $this->cartService->getCart();
        // $order = $orderRepo->findOneBy([], ['id' => 'desc']);
       
        $shipping = $shippingMethod->findOneBy(['id'=> $order->getShippingMethod()]);

        $checkoutSession = $this->stripeService
                                ->createCheckoutSession($order->getId(), 
                                                        $cart,$this->getUser()->getEmail(),
                                                        $shipping);

        return $this->render('payment/redirect.html.twig', [
            'checkoutUrl' => $checkoutSession->url,
        ]);
    }

    #[Route('/success', name: '_success', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function success(Request $request): Response
    {
     
        $sessionId = $request->query->get('session_id');
    

        if (!$sessionId) {
            throw $this->createNotFoundException('Session ID manquant.');
        }
    
        try {
            $session = Session::retrieve($sessionId);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de vérifier la session Stripe.');
            return $this->redirectToRoute('app_customer_dashboard');
        }
    
        if ($session->payment_status !== 'paid') {
            throw $this->createNotFoundException('Le paiement n’a pas été validé.');
        }
    
        $this->cartService->deleteCart();

        return $this->render('payment/success.html.twig');
    }

    #[Route('/cancel', name: '_cancel', methods: ['GET'])]
    public function cancel(): Response
    {
        $this->cartService->deleteCart();
        return $this->render('payment/cancel.html.twig');
    }
}
