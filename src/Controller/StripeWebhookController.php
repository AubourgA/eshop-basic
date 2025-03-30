<?php

namespace App\Controller;

use App\Enum\PaymentStatus;
use App\Enum\OrderStatus;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class StripeWebhookController extends AbstractController
{

    

    #[Route('/webhook/stripe', name: 'stripe_webhook', methods: ['POST'])]
    public function stripeWebhook(
                                Request $request,
                                #[Autowire('%stripe.webhook_secret%')] string $webhookSecret,
                                OrderRepository $orderRepository,
                                EntityManagerInterface $entityManager
                            ): JsonResponse
     {

       



        // Récupérer le contenu du webhook envoyé par Stripe
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            // Vérifier la signature pour s'assurer que le webhook vient bien de Stripe
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Webhook verification failed'], 400);
        }

        // Vérifier si c'est un événement de paiement réussi
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
         
            $orderId = $session->metadata->order_id ?? null; // Récupérer l'ID de la commande
          
          
            $stripePaymentId = $session->payment_intent;

            if ($orderId) {
                $order = $orderRepository->find($orderId);
                if ($order) {
                    $order->setStipePaymentID($stripePaymentId); //permet de recuperer l'id du payment pour un refund
                    $order->setPaymentStatus(PaymentStatus::PAYED->value); // Met à jour le statut
                    $order->setReference(sprintf('ORD-%s-%s',date('Ymd-His'),strtoupper(bin2hex(random_bytes(3))) ));
                    $order->setStatus(OrderStatus::PROCESSING->value);
                    $entityManager->persist($order);
                    $entityManager->flush();
                }
            }
        }

        return new JsonResponse(['status' => 'success']);
    }
}
