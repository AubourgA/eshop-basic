<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function catalog(Request $request,
                            ProductRepository $productRepo,
                            PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $productRepo->findBy(['isActive'=> true]),
            $request->query->getInt('page', 1),
            12
        );
        

        return $this->render('home/catalog.html.twig', [
            'pagination' => $pagination,
        ]);
    }


}
