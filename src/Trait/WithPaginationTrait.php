<?php

namespace App\Trait;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait WithPaginationTrait
{
     #[LiveProp(writable: true, url: true)]
    public int $page = 1;

    #[LiveProp(writable: true, url: true)]
    public int $limit = 10;

    private ?PaginationInterface $pagination = null;

    private PaginatorInterface $paginator;

    public function setPaginator(PaginatorInterface $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * Chaque composant qui utilise ce trait doit définir
     * la méthode qui fournit les données à paginer (Query|QueryBuilder|array).
     */
    abstract protected function getQueryBuilder(): mixed;

    /**
     * Récupération des résultats paginés.
     */
    public function getPaginatedResults(): PaginationInterface
    {
        if ($this->pagination === null) {
            $this->pagination = $this->paginator->paginate(
                $this->getQueryBuilder(),
                $this->page,
                $this->limit
            );
        }

        return $this->pagination;
    }
}