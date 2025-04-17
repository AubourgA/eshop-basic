<?php

namespace App\Controller\Admin;

use App\Repository\CustomerRepository;
use App\Repository\ManagerRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin')]
final class AccountAdminController extends AbstractController
{
    #[Route('/dashboard', name: '_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
           
        ]);
    }

    #[Route('/products', name: '_product', methods: ['GET'])]
    public function products(ProductRepository $productRepo): Response
    {
        return $this->render('admin/products/list_product.html.twig', [
            'products' => $productRepo->findAll(),
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

    #[Route('/employe', name: '_employe', methods: ['GET'])]
    public function employe(ManagerRepository $managerRepo): Response
    {
       
        return $this->render('admin/employe/list_employes.html.twig', [
            'employes' => $managerRepo->findAll(),
        ]);
    }
}
