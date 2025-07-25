<?php

namespace App\Entity;

use App\Repository\ProductPriceHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductPriceHistoryRepository::class)]
class ProductPriceHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productPriceHistories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le produit est requis.")]
    private ?Product $product = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "L'ancien prix est requis.")]
    #[Assert\GreaterThanOrEqual(0, message: "L'ancien prix ne peut pas être négatif.")]
    private ?float $oldPrice = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le nouveau prix est requis.")]
    #[Assert\GreaterThanOrEqual(0, message: "Le nouveau prix ne peut pas être négatif.")]
    private ?float $newPrice = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La date du changement de prix est requise.")]
    #[Assert\LessThanOrEqual('now', message: "La date de changement ne peut pas être dans le futur.")]
    private ?\DateTimeImmutable $changedAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOldPrice(): ?float
    {
        return $this->oldPrice;
    }

    public function setOldPrice(float $oldPrice): static
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    public function getNewPrice(): ?float
    {
        return $this->newPrice;
    }

    public function setNewPrice(float $newPrice): static
    {
        $this->newPrice = $newPrice;

        return $this;
    }

    public function getChangedAt(): ?\DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function setChangedAt(\DateTimeImmutable $changedAt): static
    {
        $this->changedAt = $changedAt;

        return $this;
    }
}
