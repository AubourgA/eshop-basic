<?php

namespace App\Services\Stock;

use App\Entity\Order;
use App\Entity\Stock;
use App\Services\Stock\StockManager;
use LogicException;

final class StockValidator
{
     public function __construct(private StockManager $stockManager)
    {
    }

   public function validateStockForShipping(Stock $stock,
                                            Order $order,
                                            int $requested): void
    {
         $available = $this->stockManager->getAvailableQuantityForShipping($stock, $order);

       
         if ($requested > $available) {
                throw new LogicException(
                    'Stock insuffisant pour le produit ' . $stock->getProduct()->getDesignation() .
                    '. Demandé : ' . $requested . ', disponible : ' . $available . '.'
                );
            }
    }
}