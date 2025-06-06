<?php

namespace App\Services;


use App\Entity\Stock;
use App\Entity\StockMouvement;
use App\Repository\ItemOrderRepository;
use App\Repository\OrderRepository;
use App\Utils\StockCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Service de gestion du stock.
 * 
 * Fournit des méthodes pour calculer les quantités réservées,
 * disponibles, appliquer des mouvements de stock et obtenir
 * des données enrichies sur les stocks.
 */
class StockManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ItemOrderRepository $itemOrderRepository,
        private OrderRepository $orderRepository
    ) {}

    /**
     * Calcule la quantité réservée (commandes payées et statut cde en traitement).
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
       
        return StockCalculator::calculateAvailableQuantity($stock->getQuantity(), $reserved);
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

    /**
     * Retourne un tableau de stocks enrichis avec les quantités réservées et disponibles.
     *
     * @param Stock[] $stocks Tableau d'entités Stock à analyser.
     *
     * @return array<int, array{
     *     stock: Stock,
     *     reserved: int,
     *     available: int
     * }>
     *
     * Chaque élément retourné contient :
     * - `stock` : l'entité Stock d'origine,
     * - `reserved` : la quantité réservée (commandes en cours),
     * - `available` : la quantité disponible à la vente.
     */
    public function getStocksWithCalculatedData(array $stocks): array
    {
        $stocksWithData = [];

        foreach ($stocks as $stock) {
            $reserved = $this->getReservedQuantity($stock);
            $available = $this->getAvailableQuantity($stock);

            $stocksWithData[] = [
                'stock' => $stock,
                'reserved' => $reserved,
                'available' => $available,
            ];
        }

        return $stocksWithData;
    }

    /**
     * Calcul the full stock value based on the quantity and price of each product.
     *
     * @param array $stock table of Stock entities to calculate the total value.
     * @param int $quantity quantity of the product in stock.
     *
     * @return bool True si le stock est suffisant, sinon false.
     */
    public function getFullStockValue(array $stock)
    {
        return StockCalculator::calculateFullStockValue($stock);
    }


    /**
     * Calcul the full stock quantity of products in stock.
     *
     * @param array $stock table of Stock entities to calculate the total quantity.
     *
     * @return float Total quantity of products in stock.
     */
    public function getFullStockQuantity(array $stock): float
    {
        return StockCalculator::calculateFullStockQuantity($stock);
    }


    public function getStockUnderThreshold(array $stocks): int
    {
        $count = 0;

        foreach ($stocks as $stock) {
            $available = $this->getAvailableQuantity($stock);
                if ($available <= $stock->getThreshold()) {
                    $count++;
                }
            }
        return $count;
    }
    
   
}
