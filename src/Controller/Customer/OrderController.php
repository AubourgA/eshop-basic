<?php

namespace App\Controller\Customer;

use App\Entity\Order;
use App\Entity\ShippingMethod;
use App\Security\Voter\OrderVoter;
use App\Enum\PaymentStatus;
use App\Exception\MissingShippingAddressException;
use App\Factory\OrderFactory;
use App\Repository\OrderRepository;
use App\Repository\ShippingMethodRepository;
use App\Services\CartService;
use App\Services\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[Route('/order', name: 'app_order')]
final class OrderController extends AbstractController
{
    public function __construct(private CartService $cartService)
    {   }

    #[Route('/list', name: '_list', methods: ['GET'], priority:1)]
    public function list(OrderRepository $orderRepository):Response
    {
        return $this->render('customer/order/list_orders.html.twig',[
            'orders' => $orderRepository->findBy(['customer'=>$this->getUser()])
        ]);
    }


    #[Route('/{id}/shipping', name: '_shipping', methods: ['POST'], priority:2)]
    #[IsGranted("ROLE_CUSTOMER")]
    public function updateShipping(Order $order, 
                                    Request $request, 
                                    EntityManagerInterface $entityManager): JsonResponse
    {
        $shippingMethodId = $request->request->get('shippingMethod');
     
        // Trouver la méthode de livraison sélectionnée
        $shippingMethod = $entityManager->getRepository(ShippingMethod::class)->find($shippingMethodId);
       
        if (!$shippingMethod) {
            return new JsonResponse(['success' => false, 'error' => 'Méthode de livraison non trouvée'], 400);
        }

        // Mettre à jour la commande
        $order->setShippingMethod($shippingMethod);

        // Recalculer le total de la commande
        $newTotal = $order->calculateTotal();
        $order->setTotalAmount($newTotal);

        $entityManager->flush();

        return new JsonResponse(['success' => true, 
                                'total' => number_format($newTotal, 2, ',', ' '),
                            ]);
    }


    #[Route('/create', name: '_create', methods: ['POST'], priority:3)]
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

  

    #[Route('/info/{ref}', name: '_info', methods: ['GET'], priority:4)] 
    public function orderInfo(string $ref, OrderRepository $orderRepository):Response
    {
        
        $order = $orderRepository->findOneBy(['reference' => $ref]);
        
        if (!$order) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }


        $this->denyAccessUnlessGranted(OrderVoter::VIEW, $order);
        
        return $this->render('customer/order/order_info.html.twig', [
            'order'=>$order
        ]);
    }


    #[Route('/{id}', name: '_details', methods: ['GET','POST'], priority:-1)]  
    #[IsGranted(OrderVoter::VIEW, subject: 'order')]
    public function orderDetails(Order $order, 
                                ShippingMethodRepository $shippingMethodRepository,
                                RequestStack $session,
                                CartService $cartService): Response  
    {
        if ($order->getPaymentStatus() !== PaymentStatus::PENDING) {
            $this->addFlash('warning', 'Cette commande a déjà été payé et ne peut pas être repassée.');
            return $this->redirectToRoute('app_customer_dashboard');
        }

        $cart = $session->getSession()->get('cart',[]);

        if(empty($cart)) {
            foreach ($order->getItemOrders() as $item) {
                $productId = $item->getProduct()->getId();
                $quantity = $item->getQuantity();
        
                if (!isset($cart[$productId])) {
                    $cartService->addToCart($productId, $quantity);
                }
            }
        }

      

         return $this->render('customer/order/order_detail.html.twig', [      
            'order' => $order, 
            'shippingMethods' => $shippingMethodRepository->findAll()  
             ]);  
    }


    #[Route('/{id}/invoice', name: '_invoice' , methods:['GET'])]
    public function downloadInvoice(Order $order, PdfGenerator $pdfGenerator): Response
    {
        // sécurité : vérifier que la commande appartient bien à l’utilisateur connecté
        if ($order->getCustomer() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $html = $this->renderView('invoice/invoicePDF.html.twig', [
            'order' => $order,
        ]);

        $pdfContent = $pdfGenerator->generatePdf($html);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="facture-'.$order->getReference().'.pdf"',
        ]);
    }


}
