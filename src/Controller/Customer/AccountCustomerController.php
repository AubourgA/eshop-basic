<?php

namespace App\Controller\Customer;

use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


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

    #[Route('/address', name: '_address')]
    public function address(AddressRepository $addressRepo): Response
    {


        return $this->render('customer/address.html.twig', [
            'addresses' => $addressRepo->findBy(['customer' => $this->getUser()]),
        ]);
    }
}
