<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/delivery', name: 'app_admin_delivery')]
final class AdminDeliveryController extends AbstractController
{
       #[Route('/{ref}', name: '_modal', methods:['GET'])]
    public function details(string $ref, OrderRepository $orderRepository ): Response
    {

        $order = $orderRepository->findOneBy(['reference' => $ref]);

        if (!$order) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }
        
        return $this->render('admin/delivery/modal_delivery_order.html.twig', [
          'order' => $order,
        ]);
    }
}
