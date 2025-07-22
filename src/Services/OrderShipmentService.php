<?php


namespace App\Services;

use App\Entity\ItemOrder;
use App\Entity\Order;
use App\Entity\Stock;
use App\Entity\StockMouvement;
use App\Enum\OrderStatus;
use App\Services\Stock\StockManager;
use App\Services\Stock\StockValidator;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Ce service est responsable de l’expédition des commandes. 
 * Il vérifie que chaque produit sélectionné dispose d’un stock suffisant, 
 * applique les mouvements de sortie de stock, et met à jour le statut de la commande.
 */
final class OrderShipmentService
{
    public function __construct(
        private StockManager $stockManager,
        private StockValidator $stockValidator,
    ) 
    {  }


     /**
     * Expédie la commande si tous les produits sélectionnés sont valides.
     *
     * @param Order $order La commande à expédier.
     * @param array $selectedProductIds Liste des IDs de produits sélectionnés pour l’expédition.
     * @param UserInterface $user L’utilisateur effectuant l’expédition (historique du mouvement).
     *
     * @throws \LogicException Si un produit de la commande n’est pas sélectionné ou ne peut pas être expédié.
     */
    public function shipOrder(Order $order, array $selectedProductIds, UserInterface $user): void
    {
       
        foreach ($order->getItemOrders() as $item) {
            $this->shipItemIfSelected($item, $selectedProductIds, $order, $user);
        }

        $order->setStatus(OrderStatus::SHIPPED);
    }



    /**
     * Expédie un produit de la commande s’il est sélectionné, en validant son stock.
     *
     * @param ItemOrder $item Ligne de commande à traiter.
     * @param array $selectedProductIds Liste des IDs de produits sélectionnés pour l’expédition.
     * @param Order $order La commande à laquelle la ligne appartient.
     * @param UserInterface $user L’utilisateur effectuant l’expédition.
     *
     * @throws \LogicException Si le produit n’est pas sélectionné, s’il n’a pas de stock,
     *                         ou si la quantité disponible est insuffisante.
     */
    private function shipItemIfSelected(ItemOrder $item, array $selectedProductIds, Order $order, UserInterface $user): void
    {
        $product = $item->getProduct();
        $productId = $product->getId();

    

        if (!in_array($productId, $selectedProductIds, true)) {
            throw new \LogicException(sprintf(
                'Le produit %s n\'est pas sélectionné pour la livraison.',
                $product->getDesignation()
            ));
        }

        $stock = $product->getStock();

        if (!$stock) {
            throw new \LogicException(sprintf(
                'Le produit %s n\'a pas de stock associé.',
                $product->getDesignation()
            ));
        }

        // Validation du stock disponible
        $this->stockValidator->validateStockForShipping($stock, $order, $item->getQuantity());

        // Création du mouvement de stock
        $movement = new StockMouvement();
        $movement->setType('OUT');
        $movement->setQuantity($item->getQuantity());
        $movement->setComments('Expédition de la commande ' . $order->getReference());

        $this->stockManager->applyStockMovement($stock, $movement, $user);
    }
}
    
