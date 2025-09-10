<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;

/**
 * Service Stripe pour la gestion des paiements via Stripe Checkout.
 */
class StripeService
{

    public function __construct(string $stripeSecretKey, private ParameterBagInterface $parameterBag)
    {
    
        Stripe::setApiKey($stripeSecretKey);
       
    }

    public function createCheckoutSession($id, $cartItems, $customerEmail, $shipping)
    {

       
        // Préparer les lignes de commande pour Stripe
        $lineItems = [];
        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',  // Remplace par la devise de ton choix
                    'product_data' => [
                        'name' => $item['product']->getDesignation(),
                    ],
                    'unit_amount' => $item['product']->getPrice() * 100,  // Montant en cents
                ],
                'quantity' => $item['quantity'],
            ];
        }
      
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $shipping->getName(),  
                ],
                'unit_amount' => $shipping->getPrice() * 100,  
            ],
            'quantity' => 1,  
        ];

        $domain = $this->parameterBag->get('app.domain');
        // Créer la session Stripe Checkout
        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => $customerEmail,
            'line_items' => $lineItems,
            'automatic_tax' => ['enabled' => true],
            'success_url' => $domain.'/payment/success?session_id={CHECKOUT_SESSION_ID}',  // URL de succès
            'cancel_url' => $domain.'/payment/cancel',  
            'metadata' => ['order_id' => $id]
        ]);
        

       
        return $session;
    }

}