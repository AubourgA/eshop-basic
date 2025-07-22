<?php

namespace App\Twig\Components;

use App\Entity\Order;
use App\Services\OrderShipmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class DeliveryProductSelectorComponent 
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
   
    public ?string $errorMessage = null;

    #[LiveProp]
    public Order $order;

    #[LiveProp(writable: true)]
    public array $selectedProducts = [];

 

    #[LiveAction]
    public function submit(EntityManagerInterface $em,
                            UserInterface $user,
                            OrderShipmentService $orderService): void
    {
        if (empty($this->selectedProducts)) {
             $this->errorMessage = 'Veuillez sélectionner au moins un produit pour la livraison.';
            return;
        }

        try {
            $orderService->shipOrder(   $this->order,
                                        array_map('intval', $this->selectedProducts),
                                        $user
            );

                $em->flush();

            $this->dispatchBrowserEvent('modal:close');
            
        } catch (\LogicException $e) {
            // 🔥 Capture l’erreur métier ici et envoie un message à l’utilisateur
             $this->errorMessage = $e->getMessage();
        }

    }
}
