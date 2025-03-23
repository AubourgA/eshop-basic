<?php

namespace App\Factory;

use App\Entity\Order;
use App\Entity\ItemOrder;
use App\Entity\User;
use App\Enum\PaymentStatus;
use App\Exception\MissingShippingAddressException;
use App\Repository\AddressRepository;
use App\Services\CartService;


class OrderFactory
{
    public function __construct(
        private AddressRepository $addressRepo,
        private CartService $cartService
    ) {}

    public function createOrder(User $user): ?Order
    {
        $cartSession = $this->cartService->getCart();
        
        if (count($cartSession) === 0 || is_null($cartSession)) {
            return null; // Le contrôleur pourra gérer cette situation
        }

        // Récupérer les adresses
        $shippingAddress = $this->addressRepo->findOneBy([
            'customer' => $user->getId(),
            'type' => 'livraison',
            'isPrimary' => true
        ]);

        $billingAddress = $this->addressRepo->findOneBy([
            'customer' => $user->getId(),
            'type' => 'facturation',
            'isPrimary' => true
        ]);

        if(!$shippingAddress ) {
          throw new MissingShippingAddressException("Vous devez renseigner une adresse de livraison"); 
        }

        // Création de la commande
        $order = new Order();
        $order->setCustomer($user);
        $order->setStatus('en cours');
        $order->setTotalAmount($this->cartService->getTotal());
        $order->setShippingAddress($shippingAddress);
        $order->setBillingAddress($billingAddress);
        $order->setReference(uniqid());
        $order->setPaymentStatus(PaymentStatus::PENDING->value);

        // Ajouter les produits
        foreach ($cartSession as $item) {
            $itemOrder = new ItemOrder();
            $itemOrder->setOrderNum($order);
            $itemOrder->setProduct($item['product']);
            $itemOrder->setQuantity($item['quantity']);
            $itemOrder->setUnitPrice($item['product']->getPrice());
            $order->addItemOrder($itemOrder);
        }

        return $order;
    }
}