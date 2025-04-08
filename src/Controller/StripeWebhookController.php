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

    public function __construct(private OrderRepository $orderRepository,
                               private EntityManagerInterface $entityManager)
    {  }
    

    #[Route('/webhook/stripe', name: 'stripe_webhook', methods: ['POST'])]
    public function stripeWebhook( Request $request,
                                #[Autowire('%stripe.webhook_secret%')] string $webhookSecret,            
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

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session);
            break;
            default:
            break;
        }

        return new JsonResponse(['status' => 'success']);
    }



    private function handleCheckoutSessionCompleted($session)
    {
        $orderId = $session->metadata->order_id ?? null;
        $stripePaymentId = $session->payment_intent ?? null;

        if ($orderId) {
            $order = $this->orderRepository->findOneBy(['id' => $orderId]);
            if ($order) {
                $order->setStipePaymentID($stripePaymentId);
                $order->setPaymentStatus(PaymentStatus::PAYED);
                $order->setReference(sprintf('ORD-%s-%s', date('Ymd-His'), strtoupper(bin2hex(random_bytes(3)))));
                $order->setStatus(OrderStatus::PROCESSING);
                
                $this->entityManager->persist($order);
                $this->entityManager->flush();
            }
        }
    }

}
