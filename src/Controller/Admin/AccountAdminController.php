<?php

namespace App\Controller\Admin;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Repository\CustomerRepository;
use App\Repository\ManagerRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use App\Services\DashboardDataProvider;
use App\Services\StockManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
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
    public function products(Request $request,
                        PaginatorInterface $paginator,
                            ProductRepository $productRepo): Response
    {
        //pagination
        $pagination = $paginator->paginate(
            $productRepo->findAll(),
            $request->query->getInt('page', 1), // page number
            8 // limit per page
        );
        return $this->render('admin/products/list_product.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/stock', name: '_stock', methods: ['GET'])]
    public function list(Request $request,
                        PaginatorInterface $paginator,
                        StockRepository $stockRepository,
                        StockManager $stockManager  ): Response
    {
        $stocks = $stockRepository->findAll();

        $pagination = $paginator->paginate(
            $stockManager->getStocksWithCalculatedData($stocks),
            $request->query->getInt('page', 1), 
            10 );
        
        return $this->render('admin/stocks/stock_list.html.twig', [
            'pagination' => $pagination,
            'fullStockValue' => $stockManager->getFullStockValue($stocks),
            'fullStockQuantity' => $stockManager->getFullStockQuantity($stocks),
            'productsUnderThreshold' => $stockManager->getStockUnderThreshold($stocks),
        ]);
    }

    #[Route('/order', name: '_order', methods: ['GET'])]
    public function orders(OrderRepository $orderRepo, 
                            Request $request,
                            PaginatorInterface $paginator): Response
    {
       
        $pagination = $paginator->paginate(
            $orderRepo->findAll(),
            $request->query->getInt('page', 1),
            10 );

        return $this->render('admin/orders/list_order.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/export', name: '_export', methods: ['GET'])]
    public function exportOrders(Request $request,
                                PaginatorInterface $paginator,
                                OrderRepository $orderRepo,
                                StockManager $stockManager): Response
    {
       //paginaton commande a taiter
        $pagination = $paginator->paginate(
            $orderRepo->findOrdersByStatus(OrderStatus::PROCESSING, PaymentStatus::PAYED),
            $request->query->getInt('page', 1),
            10 );

        
       
        return $this->render('admin/orders/export_orders.html.twig', [
            'pagination' => $pagination,
            'OrdersShipped' => $orderRepo->findOrdersByStatus(OrderStatus::SHIPPED, PaymentStatus::PAYED),
        ]);
    }


    #[Route('/customers', name: '_customers', methods: ['GET'])]
    public function customers(Request $request,
                            PaginatorInterface $paginator,
                             CustomerRepository $customerRepo): Response
    {
        
        $pagination = $paginator->paginate(
            $customerRepo->findAll(),
            $request->query->getInt('page', 1), 
            10 );   

        return $this->render('admin/customers/list_customers.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/employe', name: '_employe', methods: ['GET','POST'])]
    public function employe(Request $request,
                            PaginatorInterface $paginator,
                            ManagerRepository $managerRepo): Response
    {
       
        $pagination = $paginator->paginate(
            $managerRepo->findAllExceptAdmins(),
            $request->query->getInt('page', 1), 
            10 
        );
        return $this->render('admin/employe/list_employes.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
