<?php

namespace App\Controller\Admin;

use App\Entity\StockMouvement;
use App\Form\StockMouvementType;
use App\Repository\StockMouvementRepository;
use App\Repository\StockRepository;
use App\Services\StockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class AdminStockController extends AbstractController
{
     #[Route('/admin/stock/{id}', name: 'app_admin_stock_detail', methods: ['GET','POST'], priority:1)]
    public function details(int $id, 
                            Request $request,
                            StockRepository $stockRepository,
                            StockMouvementRepository $stockMouvementRepository,
                            StockManager $stockManager): Response
    {
      
        $stockProduct = $stockRepository->findOneBy(['product'=> $id]);

        // Vérifier si le stock existe
         if (!$stockProduct) {
            throw $this->createNotFoundException('Stock non trouvé');
        }

        //creer un mouvement
        $mouvement = new StockMouvement();
        $form = $this->createForm(StockMouvementType::class, $mouvement);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
             $stockManager->applyStockMovement($stockProduct, $mouvement, $this->getUser());

            return $this->redirectToRoute('app_admin_stock_detail', ['id' => $id]);
        }

        return $this->render('admin/stocks/stock_detail.html.twig', [
           'stockProduct' => $stockProduct,
           'stockMove' => $stockMouvementRepository->findBy(['stock'=> $stockProduct->getId()],['createdAt'=>'DESC']),
           'reservedQty' => $stockManager->getReservedQuantity($stockProduct),
           'availableQty' => $stockManager->getAvailableQuantity($stockProduct),
           'form'=>$form
        ]);
    }
}