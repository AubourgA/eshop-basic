<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
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

    #[Route('/list', name: '_list')]
    public function index(ProductRepository $productRepo): Response
    {
     
        return $this->render('product/index.html.twig', [
            'products' => $productRepo->findAll(),
        ]);
    }

  

    #[Route('/create', name: '_create')]
    public function create(Request $request,
                           FileUploaderService $fileUploader,
                           EntityManagerInterface $entityManager): Response
    {

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if($imageFile) {
                $newFilename = $fileUploader->uploadFile($imageFile);
                $product->setImageFileName($newFilename);
                $entityManager->persist($product);
                $entityManager->flush();
                return $this->redirectToRoute('app_product_list');
            }
        }

        return $this->render('product/product_create.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(Product $product, ProductRepository $productRepo): Response
    {

        return $this->render('product/show.html.twig', [
            'product' => $productRepo->find($product->getId()),
        ]);
    }
  

}
