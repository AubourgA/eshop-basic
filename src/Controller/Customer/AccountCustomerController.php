<?php

namespace App\Controller\Customer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account', name: 'app_customer')]
final class AccountCustomerController extends AbstractController
{
    #[Route('/dashboard', name: '_dashboard')]
    public function index(): Response
    {
        return $this->render('customer/dashboard.html.twig', [
            'controller_name' => 'AccountCustomerController',
        ]);
    }
}
