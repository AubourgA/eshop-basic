<?php

namespace App\Controller\Customer;

use App\Entity\Order;
use App\Entity\ItemOrder;
use App\Repository\AddressRepository;
use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/order', name: 'app_order')]
final class OrderController extends AbstractController
{
    public function __construct(private CartService $cartService)
    {
        
    }
    #[Route('/create', name: '_create', methods: ['POST'])]
    #[IsCsrfTokenValid('validate_cart', tokenKey: 'token')]
    public function create(AddressRepository $addressRepo, EntityManagerInterface $em): Response
    {

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {    
          return $this->redirectToRoute('app_login');      
        }    


        $cartSession = $this->cartService->getCart();
      
        if (count($cartSession) == 0 || is_null($cartSession)) {
            $this->addFlash('error', 'Your cart is empty');
            return $this->redirectToRoute('app_cart_index');
        }

        //recuperer les adress de livraison
        $shippingAddress = $addressRepo->findOneBy(['customer' => $this->getUser()->getId(), 
                                                    'type' => 'livraison',
                                                    'isPrimary' => true]);

       
        $billingAddress = $addressRepo->findOneBy(['customer' => $this->getUser()->getId(), 
                                                    'type' => 'facturation',
                                                    'isPrimary' => true]);

        $order = new Order();
        $order->setCustomer($this->getUser());
        $order->setStatus('en cours');
        $order->setTotalAmount($this->cartService->getTotal());
        $order->setShippingAddress($shippingAddress);
        $order->setBillingAddress($billingAddress);
        $order->setReference(uniqid());
        $order->setPaymentStatus('en attente');

        foreach($cartSession as $item){  
            $itemOrder = new ItemOrder();
                $itemOrder->setOrderNum($order);
                $itemOrder->setProduct($item['product']); 
               $itemOrder->setQuantity($item['quantity']); 
                $itemOrder->setUnitPrice($item['product']->getPrice());
                  $order->addItemOrder($itemOrder);  
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
