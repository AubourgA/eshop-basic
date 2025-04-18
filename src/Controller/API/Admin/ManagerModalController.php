<?php


namespace App\Controller\API\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManagerModalController extends AbstractController
{
    #[Route('/api/admin/manager/create', name: 'admin_modal_manager')]
    public function __invoke(): Response
    {
        return $this->render('admin/employe/modal_create_employe.html.twig');
    }
}
