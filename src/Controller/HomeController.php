<?php

namespace App\Controller;

use App\Form\ProductFilterType;
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

         // 1. Création du formulaire
        $form = $this->createForm(ProductFilterType::class);
        $form->handleRequest($request);

         // 2. Récupération des critères du formulaire
        $filters = $form->isSubmitted() && $form->isValid()
            ? $form->getData()
            : [];

        if ($request->query->get('search')) {
         $filters['search'] = $request->query->get('search');
        }

        // Utiliser la méthode custom
        $query = $productRepo->findByFilters($filters);


        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12
        );
        
     

        return $this->render('home/catalog.html.twig', [
            'pagination' => $pagination,
            'filterForm' => $form,
            'activeFilters' => $filters,
        ]);
    }


}
