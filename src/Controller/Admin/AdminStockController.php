<?php

namespace App\Controller\Admin;

use App\Entity\StockMouvement;
use App\Form\StockMouvementType;
use App\Repository\StockMouvementRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
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
                            EntityManagerInterface $em): Response
    {
      
        $stockProduct = $stockRepository->findOneBy(['product'=> $id]);
        $stockProductID = $stockProduct->getId();


        //creer un mouvement
        $mouvement = new StockMouvement();
        $form = $this->createForm(StockMouvementType::class, $mouvement);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $mouvement = $form->getData();
            $mouvement->setManager($this->getUser());
            $mouvement->setStock($stockProduct);
            
             if ($mouvement->getType() === 'IN') {
                $stockProduct->setQuantity($stockProduct->getQuantity() + $mouvement->getQuantity());
            } elseif ($mouvement->getType() === 'OUT') {
                $stockProduct->setQuantity($stockProduct->getQuantity() - $mouvement->getQuantity());
    }
         
            $em->persist($mouvement);
            $em->flush();
            $this->addFlash('success', 'Mouvement de stock ajouté avec succès');
            return $this->redirectToRoute('app_admin_stock_detail', ['id' => $id]);
        }

        return $this->render('admin/stocks/stock_detail.html.twig', [
           'stockProduct' => $stockProduct,
           'stockMove' => $stockMouvementRepository->findBy(['stock'=> $stockProductID]),
           'form'=>$form
        ]);
    }
}