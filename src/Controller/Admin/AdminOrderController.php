<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/order/', name: 'app_admin_order', methods:['GET'])]
final class AdminOrderController extends AbstractController
{
    #[Route('/{ref}', name: '_details', methods:['GET'])]
  
    public function details(string $ref, OrderRepository $orderRepository): Response
    {

        $order = $orderRepository->findOneBy(['reference' => $ref]);

        if (!$order) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        return $this->render('admin/orders/details_order.html.twig', [
          'order'=>$order
        ]);
    }
}
