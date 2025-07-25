<?php

namespace App\Entity;

use App\Repository\ItemOrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemOrderRepository::class)]
class ItemOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'itemOrders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La commande est obligatoire.")]
    private ?Order $orderNum = null;

    #[ORM\ManyToOne(inversedBy: 'itemOrders')]
    #[ORM\JoinColumn(nullable: false)]
     #[Assert\NotNull(message: "Le produit est obligatoire.")]
    private ?Product $product = null;

    #[ORM\Column]
     #[Assert\NotNull(message: "La quantité est obligatoire.")]
    #[Assert\Positive(message: "La quantité doit être supérieure à zéro.")]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le prix unitaire est obligatoire.")]
    #[Assert\PositiveOrZero(message: "Le prix unitaire ne peut pas être négatif.")]
    private ?float $unitPrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNum(): ?Order
    {
        return $this->orderNum;
    }

    public function setOrderNum(?Order $orderNum): static
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getTotalPrice(): float
    {
        return $this->getUnitPrice() * $this->getQuantity();
    }
}
