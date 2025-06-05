<?php

namespace App\Controller\Admin;

use App\Repository\CustomerRepository;
use App\Repository\ManagerRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use App\Services\DashboardDataProvider;
use App\Services\StockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin')]
final class AccountAdminController extends AbstractController
{
    #[Route('/dashboard', name: '_dashboard', methods: ['GET'])]
    public function index(DashboardDataProvider $dashboardDataProvider): Response
    {
        $datas = $dashboardDataProvider->getDashboardData();
        
  
        return $this->render('admin/dashboard.html.twig', [
           'customers' => $datas['customers'],
           'products' => $datas['products'],
           'ordersPayed' => $datas['ordersPayed'],
           'ordersLast' => $datas['ordersLast'],
           'bestItemSold' => $datas['bestItemSold']
        ]);
    }

    #[Route('/products', name: '_product', methods: ['GET','POST'])]
    public function products(ProductRepository $productRepo): Response
    {
        return $this->render('admin/products/list_product.html.twig', [
            'products' => $productRepo->findAll(),
        ]);
    }

    #[Route('/stock', name: '_stock', methods: ['GET'])]
    public function list(StockRepository $stockRepository,
                        StockManager $stockManager  ): Response
    {
        $stocks = $stockRepository->findAll();

       

        return $this->render('admin/stocks/stock_list.html.twig', [
            'stocksWithData' => $stockManager->getStocksWithCalculatedData($stocks),
            'fullStockValue' => $stockManager->getFullStockValue($stocks),
            'fullStockQuantity' => $stockManager->getFullStockQuantity($stocks),
            'productsUnderThreshold' => $stockManager->getStockUnderThreshold($stocks),
        ]);
    }

    #[Route('/order', name: '_order', methods: ['GET'])]
    public function orders(OrderRepository $orderRepo): Response
    {
       
        return $this->render('admin/orders/list_order.html.twig', [
            'orders' => $orderRepo->findAll(),
        ]);
    }

    #[Route('/customers', name: '_customers', methods: ['GET'])]
    public function customers(CustomerRepository $customerRepo): Response
    {
       
        return $this->render('admin/customers/list_customers.html.twig', [
            'customers' => $customerRepo->findAll(),
        ]);
    }

    #[Route('/employe', name: '_employe', methods: ['GET','POST'])]
    public function employe(ManagerRepository $managerRepo): Response
    {
       
        return $this->render('admin/employe/list_employes.html.twig', [
            'employes' => $managerRepo->findAllExceptAdmins(),
        ]);
    }
}
