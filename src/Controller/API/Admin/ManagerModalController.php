<?php


namespace App\Controller\API\Admin;

use App\Entity\Product;
use App\Entity\Manager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ManagerModalController extends AbstractController
{
    #[Route('/api/admin/manager/create', name: 'admin_modal_manager')]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(): Response
    {
        return $this->render('admin/employe/modal_create_employe.html.twig');
    }
    
    #[Route('/api/admin/{id}/edit', name: 'admin_edit_manager')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Manager $manager): Response
    {
        
        return $this->render('admin/employe/modal_create_employe.html.twig', [
            'manager' => $manager
        ]);
    }
    
    #[Route('/api/admin/product', name: 'admin_modal_product', methods:['GET'])]
    #[IsGranted('ROLE_PRODUCT')]
    public function new_product(): Response
    {
        return $this->render('admin/products/modal_product.html.twig');
    }
    
    #[Route('/api/admin/product/{id}', name: 'admin_modal_product_edit', methods:['GET'])]
    #[IsGranted('ROLE_PRODUCT')]
    public function edit_product(int $id, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
    
       
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }
    
        return $this->render('admin/products/modal_product_edit.html.twig', [
            'product' => $product,
        ]);
    }


}
