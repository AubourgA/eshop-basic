<?php

namespace App\Services;

use App\Entity\Stock;
use App\Entity\StockMouvement;
use App\Repository\ItemOrderRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class StockManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ItemOrderRepository $itemOrderRepository
    ) {}

    /**
     * Calcule la quantité réservée (commandes payées et en traitement).
     */
    public function getReservedQuantity(Stock $stock): int
    {
        return $this->itemOrderRepository->getReservedQuantityForProduct($stock->getProduct());
    }

    /**
     * Calcule la quantité disponible à la vente.
     */
    public function getAvailableQuantity(Stock $stock): int
    {
        $reserved = $this->getReservedQuantity($stock);
        return max($stock->getQuantity() - $reserved, 0);
    }

    /**
     * Crée un mouvement de stock (entrée ou sortie) et met à jour la quantité.
     */
    public function applyStockMovement(Stock $stock, 
                                        StockMouvement $mouvement, 
                                        UserInterface $manager): void
    {
        $mouvement->setStock($stock);
        $mouvement->setManager($manager);

        if ($mouvement->getType() === 'IN') {
            $stock->setQuantity($stock->getQuantity() + $mouvement->getQuantity());
        } elseif ($mouvement->getType() === 'OUT') {
            $stock->setQuantity($stock->getQuantity() - $mouvement->getQuantity());
        }

        $this->em->persist($mouvement);
        $this->em->flush();
    }
}
