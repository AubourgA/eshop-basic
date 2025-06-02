<?php

namespace App\Controller\Admin;

use App\Repository\StockMouvementRepository;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class AdminStockController extends AbstractController
{
     #[Route('/admin/stock/{id}', name: 'app_admin_stock_detail', methods: ['GET'], priority:1)]
    public function details(int $id, 
                            StockRepository $stockRepository,
                            StockMouvementRepository $stockMouvementRepository): Response
    {
      
        $stockProduct = $stockRepository->findOneBy(['product'=> $id]);
        $stockProductID = $stockProduct->getId();

       
     

        return $this->render('admin/stocks/stock_detail.html.twig', [
           'stockProduct' => $stockProduct,
           'stockMove' => $stockMouvementRepository->findBy(['stock'=> $stockProductID])
        ]);
    }
}