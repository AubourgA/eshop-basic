<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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


        /**
     * Recherche les produits selon des filtres applicables
     * 
     * Cette méthode retourne un QueryBuilder pour permettre une pagination optimisée
     * plutôt que de charger tous les résultats en mémoire
     *
     * @param array $filters Tableau associatif des critères de filtrage
     *                      - 'categories' (array|null) : Tableau d'IDs de catégories
     *                      - 'price' (float|null) : Prix maximum inclusif
     * 
     * @return QueryBuilder QueryBuilder configuré avec les filtres appliqués
     * 
     * 
     * @throws \Doctrine\ORM\Query\QueryException Si les paramètres sont invalides
     */
    public function findByFilters(array $filters): QueryBuilder
    {

        $qb = $this->createQueryBuilder('p')
        ->where('p.isActive = :active')
        ->setParameter('active', true);

        if (!empty($filters['categories'])) {
            $categories = array_filter($filters['categories']->toArray());
                    if ($categories) { // uniquement si le tableau contient au moins une valeur
                        $qb->andWhere('p.category IN (:categories)')
                        ->setParameter('categories', $categories);
                    }
        }
        // Prix max
        if (isset($filters['price']) && $filters['price'] > 0) {
            $qb->andWhere('p.price <= :maxPrice')
            ->setParameter('maxPrice', (float) $filters['price']);
        }


            return $qb;
    }

}
