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
}
