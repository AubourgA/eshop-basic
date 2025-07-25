<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductPriceHistoryRepository;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use App\Services\Stock\StockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/product', name: 'app_product')]
class ProductController extends AbstractController
{
    /**
     * fiche produit accecible public
     */
    #[Route('/{id}', name: '_detail', methods: ['GET'], priority:-1)]
    public function detail(Product $product, 
                            ProductRepository $productRepo,
                            StockRepository $stockRepository,
                            StockManager $stockManager): Response
    {

        $stock = $stockRepository->findOneBy(['product' => $product]);

        $availableQty = null;

        if ($stock) {
            $availableQty = $stockManager->getAvailableQuantity($stock);
        }

        return $this->render('product/detail.html.twig', [
            'product' => $productRepo->find($product->getId()),
            'availableQty' => $availableQty,
        ]);
    }
  

    /**
     * Recuperere les enregistrement de l'évolution du prix de vente selon le produit
     * dans la partie ADMIN
     */
    #[Route('/admin/historic/{id}', name: '_admin_historic', methods: ['GET'], priority:2)]
    #[IsGranted('ROLE_PRODUCT')]
    public function historic(Product $product, ProductPriceHistoryRepository $histoRepo):Response
    {

        return $this->render('admin/products/historic_product.html.twig', [
            'productHistoPrice' => $histoRepo->findBy(['product' => $product])
        ]);
    }
}
