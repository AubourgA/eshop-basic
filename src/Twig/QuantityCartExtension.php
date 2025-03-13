<?php

namespace App\Twig;


use Twig\Environment;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use App\Services\CartService;

class QuantityCartExtension extends AbstractExtension
{
    
  

    public function __construct(private Environment $twig, private CartService $cartService)
    {        }
    /**
     * create fcn for Twig environment
     *
     * @return array
     */    
    public function getFunctions():array
    {
        return [
            new TwigFunction('quantityCart', [$this, 'getQuantityCart'], ['is_safe'=> ['html']])
        ];
    }

    /**
     * define fcn : get data from DB and render to view
     *
     * @return int
     */
    public function getQuantityCart():int
    {
       $quantitiyCart = array_sum(array_column($this->cartService->getCart(),'quantity'));
        
       return $this->twig->render('partials/_badgeCart.html.twig', [
            'quantityCart' => $quantitiyCart
       ]);
    }
}