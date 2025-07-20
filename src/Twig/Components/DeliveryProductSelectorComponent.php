<?php

namespace App\Twig\Components;

use App\Entity\Order;
use App\Entity\StockMouvement;
use App\Enum\OrderStatus;
use App\Services\StockManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Instanceof_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class DeliveryProductSelectorComponent 
{
    use DefaultActionTrait;
   

    #[LiveProp]
    public Order $order;

    #[LiveProp(writable: true)]
    public array $selectedProducts = [];

 

    #[LiveAction]
    public function submit(StockManager $stockManager,
                            EntityManagerInterface $em,
                            UserInterface $user): void
    {

       
        if (empty($this->selectedProducts)) {
            return;
        }

        // ✅ Cast toutes les valeurs en int une seule fois
        $selectedProductIds = array_map('intval', $this->selectedProducts);

     

        foreach ($this->order->getItemOrders() as $item) {
            $product = $item->getProduct();
            $productId = $product->getId();

          

            if(!in_array($productId, $selectedProductIds, true)) {
                throw new \LogicException('Le produit '.$product->getDesignation().' n\'est pas sélectionné pour la livraison.');
            }
            
            $stock = $product->getStock();

            if(!$stock) {
                throw new \LogicException('Le produit '.$product->getDesignaton().' n\'a pas de stock associé.');
            }


            $movement = new StockMouvement();
            $movement->setType('OUT');
            $movement->setQuantity($item->getQuantity());
            $movement->setComments('Expédition de la commande '.$this->order->getReference());

            $stockManager->applyStockMovement($stock, $movement, $user);
            
         }

        $this->order->setStatus(OrderStatus::SHIPPED);

        $em->flush();
    }
}
