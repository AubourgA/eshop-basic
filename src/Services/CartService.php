<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final class CartService
{
    

    public function __construct(private RequestStack $session,
                                private ProductRepository $productRepository)
    {
    }

    public function addToCart(int $productID, $quantity): void
    {
        $cart = $this->session->getSession()->get('cart', []);
        if(!empty($cart[$productID])) {
            $cart[$productID] += $quantity;
           
        } else {
            $cart[$productID] = 1;
        }


        $this->session->getSession()->set('cart', $cart);
     
    }


    public function removeToCart(int $productID): void
    {
        $cart = $this->session->getSession()->get('cart', []);
        if(!empty($cart[$productID])) {
            unset($cart[$productID]);
        }
        $this->session->getSession()->set('cart', $cart);
    }

    public function getCart(): array
    {
        $cart = $this->session->getSession()->get('cart', []);
        $cartWithData = [];
        foreach($cart as $productID => $quantity) {
            $cartWithData[] = [
                'product' => $this->productRepository->find($productID),
                'quantity' => $quantity
            ];
        }
        return $cartWithData;
    }
}