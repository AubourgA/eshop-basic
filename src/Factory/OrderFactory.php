<?php

namespace App\Factory;

use App\Entity\Order;
use App\Entity\ItemOrder;
use App\Entity\User;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Exception\MissingShippingAddressException;
use App\Repository\AddressRepository;
use App\Repository\ShippingMethodRepository;
use App\Services\CartService;


class OrderFactory
{
    public function __construct(
        private AddressRepository $addressRepo,
        private ShippingMethodRepository $shippingMethodRepository,
        private CartService $cartService
    ) {}

    public function createOrder(User $user): ?Order
    {
        $cartSession = $this->cartService->getCart();
        
        if (count($cartSession) === 0 || is_null($cartSession)) {
            return null; 
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

        //Recuperer la methode de livraion par default
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['name'=>'Colissimo']);

        // Création de la commande
        $order = new Order();
        $order->setCustomer($user);
        $order->setStatus(OrderStatus::IN_PROGRESS);
        $order->setTotalAmount($this->cartService->getTotal());
        $order->setShippingAddress($shippingAddress);
        $order->setBillingAddress($billingAddress);
        $order->setShippingMethod($shippingMethod);
        $order->setReference('PEND-'.uniqid());
        $order->setPaymentStatus(PaymentStatus::PENDING);

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