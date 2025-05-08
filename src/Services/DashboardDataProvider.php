<?php

namespace App\Services;

use App\Enum\PaymentStatus;
use App\Repository\CustomerRepository;
use App\Repository\ItemOrderRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;

final class DashboardDataProvider
{

    public function __construct(private CustomerRepository $customerRepository, 
                                private ProductRepository $productRepository,
                                private OrderRepository $orderRepository,
                                private ItemOrderRepository $itemOrderRepository)
    { }

    public function getDashboardData(): array
    {
        return [
            'customers' => $this->customerRepository->findAll(),
            'products' => $this->productRepository->findAll(),
            'ordersLast' => $this->orderRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'ordersPayed' => $this->orderRepository->findBy(['paymentStatus' => PaymentStatus::PAYED]),
            'bestItemSold' => $this->itemOrderRepository->findMostSoldProducts(5)
        ];
    }
}