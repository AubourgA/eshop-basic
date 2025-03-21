<?php

namespace App\Controller\Customer;

use App\Entity\Order;

use App\Exception\MissingShippingAddressException;
use App\Factory\OrderFactory;
use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/order', name: 'app_order')]
final class OrderController extends AbstractController
{
    public function __construct(private CartService $cartService)
    {   }

    #[Route('/create', name: '_create', methods: ['POST'])]
    #[IsCsrfTokenValid('validate_cart', tokenKey: 'token')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create( EntityManagerInterface $em,
                            OrderFactory $orderFactory): Response
    {

        try {
            $order = $orderFactory->createOrder($this->getUser());
        } catch(MissingShippingAddressException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('app_address_create');
        } catch (\LogicException $e) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('app_customer_dashboard');
        }

    

        $em->persist($order);
        $em->flush();
       
        return $this->redirectToRoute('app_order_details', ['id' => $order->getId()]); 

    }


    #[Route('/{id}', name: '_details', methods: ['GET','POST'])]  
    public function orderDetails(Order $order): Response  
    {
                 return $this->render('customer/order/order_detail.html.twig', [      
        'order' => $order,        ]);  
    }
    
}
