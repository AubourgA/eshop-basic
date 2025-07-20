<?php

// This service will handle the logic for shipping orders.
// It will interact with the DeliveryProductSelectorComponent to manage product selection for delivery.

namespace App\Services;

use App\Entity\Order;
use App\Entity\StockMouvement;
use App\Enum\OrderStatus;
use Symfony\Component\Security\Core\User\UserInterface;

final class OrderShipmentService
{
    public function __construct(private StockManager $stockManager)
    {    }   

    public function shipOrder(Order $order, 
                             array $selectedProductIds,
                             UserInterface $user): void
    {
      

        foreach($order->getItemOrders() as $item) {
            $product = $item->getProduct();
            $productId = $product->getId();

            if (!in_array($productId, $selectedProductIds, true)) {
                throw new \LogicException('Le produit ' . $product->getDesignation() . ' n\'est pas sélectionné pour la livraison.');
            }
            
            $stock = $product->getStock();

            if(!$stock) {
                throw new \LogicException('Le produit '.$product->getDesignaton().' n\'a pas de stock associé.');
            }

            $movement = new StockMouvement();
            $movement->setType('OUT');
            $movement->setQuantity($item->getQuantity());
            $movement->setComments('Expédition de la commande '.$order->getReference());

            $this->stockManager->applyStockMovement($stock, $movement, $user);

        }

         $order->setStatus(OrderStatus::SHIPPED);
    }
}

    
