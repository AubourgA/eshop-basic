<?php

namespace App\EventListener;

use App\Entity\Product;
use App\Entity\ProductPriceHistory;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostUpdateEventArgs;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Product::class)]
class ProductPriceListener
{
    public function postUpdate(Product $product, PostUpdateEventArgs $event): void
    {
      
         $uow = $event->getObjectManager()->getUnitOfWork();
         $changeSet = $uow->getEntityChangeSet($product);

 
        if (!isset($changeSet['price'])) {
            return; // Pas de changement de prix, on quitte
        }

        [$oldPrice, $newPrice] = $changeSet['price'];
    

        $history = new ProductPriceHistory();
        $history->setProduct($product);
        $history->setOldPrice($oldPrice);
        $history->setNewPrice($newPrice);
        $history->setChangedAt(new \DateTimeImmutable());
    

        $em = $event->getObjectManager();
        $em->persist($history);
        $em->flush();
        
    }
}