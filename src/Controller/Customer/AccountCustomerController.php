<?php

namespace App\Controller\Customer;

use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


#[Route('/account', name: 'app_customer')]
final class AccountCustomerController extends AbstractController
{
    #[Route('/dashboard', name: '_dashboard')]
    public function index(ManagerRegistry $manger): Response
    {
        $em = $manger->getManager();

        $orders = $em->getRepository('App\Entity\Order')->findBy(['customer' => $this->getUser()],[ 'createdAt' => 'DESC']);
        $address = $em->getRepository('App\Entity\Address')->findOneBy(['customer' => $this->getUser(),'type' => 'livraison', 'isPrimary' => true]);
        return $this->render('customer/dashboard.html.twig', [
           
            'orders' => $orders,
            'address' => $address
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
