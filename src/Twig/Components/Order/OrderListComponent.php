<?php

namespace App\Twig\Components\Order;


use App\Repository\OrderRepository;
use App\Trait\WithPaginationTrait;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class OrderListComponent
{
    use DefaultActionTrait;
    use WithPaginationTrait;

    #[LiveProp(writable: true, url: true, onUpdated: 'onQueryUpdated')]
    public string $query = '';



    public function __construct(
        private OrderRepository $orderRepository,
        private PaginatorInterface $paginator,
    ) {
          $this->setPaginator($paginator);
    }

    
    protected function getQueryBuilder(): mixed
    {
        return $this->orderRepository->searchByReference(trim($this->query));
    }

  public function onQueryUpdated(): void
    {
        // Dès que la query change, on remet la pagination à 1
        $this->page = 1;
        $this->pagination = null;
    }


      public function getOrders()
    {
        return $this->getPaginatedResults();
    }             
  

    

  
    

    



    
}
