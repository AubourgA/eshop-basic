<?php

namespace App\Twig\Components\Address;;

use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class AddressCardComponent
{
    use DefaultActionTrait;

    public function __construct(private readonly EntityManagerInterface $em)
    {   }

    #[LiveProp]
    public Address $address;

    #[LiveAction]
    public function togglePrimary(): void
    {
       
    $customer = $this->address->getCustomer();
    $type = $this->address->getType();

      
    // 1️⃣ On récupère toutes les adresses de ce client du même type
    foreach ($customer->getAddresses() as $addr) {
        if ($addr->getType() === $type) {
            $addr->setIsPrimary(false);
        }
    }

    // 2️⃣ On définit celle-ci comme principale
    $this->address->setIsPrimary(true);

    // 3️⃣ On sauvegarde
      $this->em->flush();
    }

}
