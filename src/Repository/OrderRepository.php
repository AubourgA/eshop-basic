<?php

namespace App\Repository;

use App\Entity\Order;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

 
   /**
 * Récupère les commandes en fonction de leur statut de traitement et de paiement.
 *
 * @param string $status        Le statut de traitement de la commande (ex: 'en_traitement').
 * @param string $paymentStatus Le statut de paiement de la commande (ex: 'payed').
 *
 * @return Order[] Retourne un tableau d'entités Order correspondant aux critères.
 */
    public function findOrdersByStatus(OrderStatus $status, ?PaymentStatus $paymentStatus = null ): array
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->setParameter('status', $status);

            if($paymentStatus !== null) {
           
           $qb->andWhere('o.paymentStatus = :paymentStatus')
            ->setParameter('paymentStatus', $paymentStatus);
            }
        $qb->orderBy('o.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère les commandes passées par mois.
     *
     * @return Order[] Retourne un tableau d'entités Order correspondant aux commandes selon les mois.
     */
    // public function countOrdersByMonth(): array
    // {
    //     $qb = $this->createQueryBuilder('o')
    //         ->select('MONTH(o.createdAt) as month, COUNT(o.id) as orderCount')
    //         ->groupBy('month')
    //         ->orderBy('month', 'ASC');

    //     return $qb->getQuery()->getResult();
    // }

     public function countOrdersByMonth(): array
    {
        $startDate = (new \DateTimeImmutable('first day of -11 months'))->setTime(0, 0);

        $qb = $this->createQueryBuilder('o')
            ->select("DATE_FORMAT(o.createdAt, '%Y-%m') as yearMonth, COUNT(o.id) as orderCount")
            ->where('o.createdAt >= :startDate')
            ->andWhere('o.paymentStatus = :paymentStatus')
            ->setParameter('startDate', $startDate)
            ->setParameter('paymentStatus', PaymentStatus::PAYED)
            ->groupBy('yearMonth')
            ->orderBy('yearMonth', 'ASC');

        return $qb->getQuery()->getResult();
    }


    /**
     * Récupère le chiffre d'affaires mensuel des 12 derniers mois.
     *
     * @return array Retourne un tableau associatif avec les mois et le chiffre d'affaires correspondant.
     */
    public function getMonthlyRevenueLast12Months(): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select("DATE_FORMAT(o.createdAt, '%Y-%m') AS yearMonth, SUM(o.totalAmount) AS revenue")
            ->where('o.paymentStatus = :paymentStatus') 
            ->setParameter('paymentStatus', PaymentStatus::PAYED)
            ->groupBy('yearMonth')
            ->orderBy('yearMonth', 'ASC')
            ->setMaxResults(12);

        return $qb->getQuery()->getResult();
    }


    /**
     * Recherche des commandes par référence.
     *
     * @param string|null $reference La référence de la commande à rechercher.
     *
     * @return \Doctrine\ORM\QueryBuilder Retourne un QueryBuilder pour les commandes correspondantes.
     */
    public function searchByReference(?string $reference): array
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC');

        if (!empty($reference)) {
            $qb->where('o.reference LIKE :ref')
            ->setParameter('ref', '%' . $reference . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
