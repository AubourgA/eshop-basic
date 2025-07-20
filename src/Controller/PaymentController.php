<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ShippingMethodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\CartService;
use Stripe\Checkout\Session;
use App\Services\StripeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur PaymentController
 *
 * Ce contrôleur gère tout le processus de paiement des commandes via Stripe Checkout.
 * Il permet :
 * 
 * - La création d'une session Stripe Checkout à partir d'une commande existante
 * - La redirection sécurisée de l'utilisateur vers l’interface de paiement Stripe
 * - Le traitement du retour en cas de succès ou d’annulation du paiement
 *
 *
 * 🔁 Cycle de paiement :
 * 1. `checkout/{id}` : Crée une session de paiement Stripe pour la commande `Order` donnée.
 * 2. `/success` : Affiche une page de succès si Stripe confirme le paiement.
 * 3. `/cancel` : Affiche une page d'annulation, en cas d’abandon du paiement par l’utilisateur.
 *
 * 💳 Technologies :
 * - Stripe API (via `StripeService`)
 * - Doctrine (pour accéder aux entités `Order` et `ShippingMethod`)
 * - `CartService` pour gérer le panier utilisateur
 *
 * 📁 Templates utilisés :
 * - `payment/redirect.html.twig`
 * - `payment/success.html.twig`
 * - `payment/cancel.html.twig`
 *
 * @see \App\Services\StripeService pour la logique de création de session Stripe
 * @see \App\Entity\Order pour les commandes client
 */

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
    #[IsGranted("ROLE_CUSTOMER")]
    public function index(Order $order, 
                         ShippingMethodRepository $shippingMethod): Response
    {
       
        $cart = $this->cartService->getCart();
       
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
    #[IsGranted("ROLE_CUSTOMER")]
    public function cancel(): Response
    {
        $this->cartService->deleteCart();
        return $this->render('payment/cancel.html.twig');
    }
}
