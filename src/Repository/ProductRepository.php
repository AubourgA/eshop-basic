<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

   public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.isActive = :active')
            ->setParameter('active', true);

        // Catégories
        if (!empty($filters['categories'])) {
            $qb->andWhere('p.category IN (:cats)')
            ->setParameter('cats', $filters['categories']);
        }

        // Prix max
        if (!empty($filters['price'])) {
            $qb->andWhere('p.price <= :maxPrice')
            ->setParameter('maxPrice', $filters['price']);
        }


        return $qb->getQuery()->getResult();
    }
}
