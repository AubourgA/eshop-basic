<?php

namespace App\Repository;

use App\Entity\ItemOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\PaymentStatus;

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
}
