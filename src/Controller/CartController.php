<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use App\Services\CartService;
use Symfony\Component\HttpFoundation\Request;

final class CartController extends AbstractController
{
    public function __construct(private CartService $cartService)
    {
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {

      
        return $this->render('cart/index.html.twig', [
            'cart' => $this->cartService->getCart(),
            'shippingMethod' => $this->cartService->getDefaultShippingMethod(),
        ]);
    }


    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    #[IsCsrfTokenValid('add_cart', tokenKey: 'token')]
    public function add(int $id, Request $request): Response
    {
        $quantity = (int) $request->request->get('quantity');
       
        if ($quantity < 1) {
            $this->addFlash('error', 'La quantité ne peut pas être inférieure à 1.');
            return $this->redirectToRoute('app_cart');
       }

        $this->cartService->addToCart($id, $quantity);
       
        return $this->redirectToRoute('app_catalog');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    #[IsCsrfTokenValid('remove_cart', tokenKey: 'token')]
    public function remove(int $id): Response
    {
        $this->cartService->removeToCart($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['POST'])]
    #[IsCsrfTokenValid('clear_cart', tokenKey: 'token')]
    public function clear(): Response
    {
        $this->cartService->deleteCart();
        return $this->redirectToRoute('app_cart');
    }


}
