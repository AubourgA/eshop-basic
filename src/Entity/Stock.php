<?php

namespace App\Entity;

use App\Repository\StockRepository;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: StockRepository::class)]
#[ORM\Table(name: '`stock`')]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La quantité est requise.")]
    #[Assert\GreaterThanOrEqual(0, message: "La quantité ne peut pas être négative.")]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le seuil est requis.")]
    #[Assert\GreaterThanOrEqual(0, message: "Le seuil de stock ne peut pas être négatif.")]
    private ?int $threshold = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(inversedBy: 'stock', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Un produit doit être associé à ce stock.")]
    private ?Product $product = null;

    /**
     * @var Collection<int, StockMouvement>
     */
    #[ORM\OneToMany(targetEntity: StockMouvement::class, mappedBy: 'stock')]
    private Collection $stockMouvements;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
        $this->stockMouvements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getThreshold(): ?int
    {
        return $this->threshold;
    }

    public function setThreshold(int $threshold): static
    {
        $this->threshold = $threshold;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

     #[ORM\PreUpdate]
    public function setUpdatedAtValue()
    {
        $this->updatedAt =new \DateTimeImmutable('now');
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection<int, StockMouvement>
     */
    public function getStockMouvements(): Collection
    {
        return $this->stockMouvements;
    }

    public function addStockMouvement(StockMouvement $stockMouvement): static
    {
        if (!$this->stockMouvements->contains($stockMouvement)) {
            $this->stockMouvements->add($stockMouvement);
            $stockMouvement->setStock($this);
        }

        return $this;
    }

    public function removeStockMouvement(StockMouvement $stockMouvement): static
    {
        if ($this->stockMouvements->removeElement($stockMouvement)) {
            // set the owning side to null (unless already changed)
            if ($stockMouvement->getStock() === $this) {
                $stockMouvement->setStock(null);
            }
        }

        return $this;
    }
}
