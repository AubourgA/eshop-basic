<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepo): Response
    {
        return $this->render('home/index.html.twig', [
            'lastProducts' => $productRepo->findBy([],[ 'id' => 'DESC'], 3),
        ]);
    }

    #[Route('/catalog', name: 'app_catalog')]
    public function catalog(ProductRepository $productRepo): Response
    {
        return $this->render('home/catalog.html.twig', [
            'products' => $productRepo->findAll(),
        ]);
    }


}
