<?php

namespace App\Controller\Customer;

use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Address;
use App\Form\AddressType;

#[Route('/customer', name: 'app_address')]
final class AddressController extends AbstractController
{
    #[Route('/create', name: '_create')]
    public function create(Request $request, 
                           EntityManagerInterface $em, 
                          ): Response
    {

        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setCustomer($this->getUser());
            $em->persist($address);

            // Si l'utilisateur veut la même adresse pour la facturation
            if ($form->get('useAsBilling')->getData() && $address->getType() === 'livraison') {
                $billingAddress = new Address();
                $billingAddress->setStreet($address->getStreet());
                $billingAddress->setCity($address->getCity());
                $billingAddress->setPostalCode($address->getPostalCode());
                $billingAddress->setCountry($address->getCountry());
                $billingAddress->setCustomer($this->getUser());
                $billingAddress->setType('facturation'); 
                $billingAddress->setIsPrimary($address->isPrimary()); 

                $em->persist($billingAddress);
            }

            $em->flush();

            return $this->redirectToRoute('app_customer_address');
        }
        return $this->render('customer/address/create_address.html.twig', [
          'form' => $form
        ]);
    }
}
