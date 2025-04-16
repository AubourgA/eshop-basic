<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin')]
final class AccountAdminController extends AbstractController
{
    #[Route('/dashboard', name: '_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
           
        ]);
    }
}
