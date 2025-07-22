<?php

namespace App\Repository;

use App\Entity\ItemOrder;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\PaymentStatus;
use App\Enum\OrderStatus;

/**
 * @extends ServiceEntityRepository<ItemOrder>
 */
class ItemOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemOrder::class);
    }


    /**
     * Récupère les produits les plus vendus en fonction des quantités totales vendues,
     * uniquement sur les commandes dont le statut est PAYED.
     *
     * @param int $limit Nombre maximum de produits à retourner (par défaut 10)
     * @return array Retourne un tableau d'objets contenant :
     *               - productName (l'entité Product)
     *               - totalSold (nombre total d'unités vendues)
     */
    public function findMostSoldProducts(int $limit = 10): array
    {
        return $this->createQueryBuilder('oi')
        ->join('oi.product', 'p')
        ->join('oi.orderNum', 'o')
        ->select('p.designation AS productName, SUM(oi.quantity) AS totalSold')
        ->where('o.paymentStatus = :status')
        ->setParameter('status', PaymentStatus::PAYED)
        ->groupBy('p.id')
        ->orderBy('totalSold', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
    }

    /**
     * Récupère la quantité totale de produits réservés pour un produit spécifique,
     * uniquement pour les commandes dont le statut est PROCESSING et le paiement est PAYED.
     *
     * @param Product $product L'entité Product pour laquelle on veut connaître la quantité réservée
     * @return int Retourne la quantité totale réservée pour le produit
     */
    public function getReservedQuantityForProduct(Product $product): int
    {
        $qb = $this->createQueryBuilder('io')
            ->select('SUM(io.quantity)')
            ->join('io.orderNum', 'o')
            ->where('io.product = :product')
            ->andWhere('o.paymentStatus = :paid')
            ->andWhere('o.status = :status')
            ->setParameter('product', $product)
            ->setParameter('paid', PaymentStatus::PAYED)
            ->setParameter('status', OrderStatus::PROCESSING);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }


/**
 * Calcule la quantité totale réservée d’un produit dans les commandes en cours (PROCESSING),
 * en excluant une commande donnée.
 *
 * Utile pour vérifier la quantité réellement réservée sans prendre en compte la commande en cours de modification.
 *
 * @param Product $product Le produit pour lequel on souhaite connaître la quantité réservée.
 * @param Order   $excludedOrder La commande à exclure du calcul (souvent la commande en cours).
 *
 * @return int La quantité totale réservée du produit (hors commande courante). Retourne 0 si aucune réservation.
 */
  public function getReservedQuantityForProductExcludingOrder(Product $product, Order $excludedOrder): int
    {
        $qb = $this->createQueryBuilder('io')
            ->select('SUM(io.quantity)')
            ->join('io.orderNum', 'o')
            ->where('io.product = :product')
            ->andWhere('o.status = :processing')
            ->setParameter('product', $product)
            ->setParameter('processing', OrderStatus::PROCESSING);

            if ($excludedOrder->getId() !== null) {
                $qb->andWhere('o.id != :excludedOrderId')
                ->setParameter('excludedOrderId', $excludedOrder->getId());

            }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

}
