<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Stock;
use App\Entity\ProductPriceHistory;
use App\Form\ProductType;
use App\Repository\ProductPriceHistoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\FileUploaderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product', name: 'app_product')]
class ProductController extends AbstractController
{

    /**
     * fiche produit accecible public
     */
    #[Route('/{id}', name: '_detail', methods: ['GET'], priority:-1)]
    public function detail(Product $product, ProductRepository $productRepo): Response
    {

        return $this->render('product/detail.html.twig', [
            'product' => $productRepo->find($product->getId()),
        ]);
    }
  

    /**
     * Recuperere les enregistrement de l'évolution du prix de vente selon le produit
     * dans la partie ADMIN
     */
    #[Route('/admin/historic/{id}', name: '_admin_historic', methods: ['GET'], priority:2)]
    public function historic(Product $product, ProductPriceHistoryRepository $histoRepo):Response
    {

        return $this->render('admin/products/historic_product.html.twig', [
            'productHistoPrice' => $histoRepo->findBy(['product' => $product])
        ]);
    }
 
  

}
