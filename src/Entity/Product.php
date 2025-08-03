<?php

namespace App\Entity;


use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\MarketingPosition;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: "La designation du produit est obligatoire.")]
    private ?string $designation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description du produit est obligatoire.")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être positif.")]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFileName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, ItemOrder>
     */
    #[ORM\OneToMany(targetEntity: ItemOrder::class, mappedBy: 'product')]
    private Collection $itemOrders;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: "Le coût d’achat ne peut pas être négatif.")]
    private ?float $purchasePrice = null;

    #[ORM\Column(type:'string', enumType: MarketingPosition::class, nullable: true)]
    private ?MarketingPosition $marketingPosition = null;

    /**
     * @var Collection<int, ProductPriceHistory>
     */
    #[ORM\OneToMany(targetEntity: ProductPriceHistory::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $productPriceHistories;

    #[ORM\OneToOne(mappedBy: 'product', cascade: ['persist', 'remove'])]
    private ?Stock $stock = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->itemOrders = new ArrayCollection();
        $this->productPriceHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName): static
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, ItemOrder>
     */
    public function getItemOrders(): Collection
    {
        return $this->itemOrders;
    }

    public function addItemOrder(ItemOrder $itemOrder): static
    {
        if (!$this->itemOrders->contains($itemOrder)) {
            $this->itemOrders->add($itemOrder);
            $itemOrder->setProduct($this);
        }

        return $this;
    }

    public function removeItemOrder(ItemOrder $itemOrder): static
    {
        if ($this->itemOrders->removeElement($itemOrder)) {
            // set the owning side to null (unless already changed)
            if ($itemOrder->getProduct() === $this) {
                $itemOrder->setProduct(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPurchasePrice(): ?float
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(float $purchasePrice): static
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    public function getMargin(): ?float
    {
        if ($this->purchasePrice !== null && $this->price !== null && $this->purchasePrice > 0) {
            return round((($this->price - $this->purchasePrice) / $this->purchasePrice) * 100, 2);
        }

        return null;
    }

    public function getMarketingPosition(): ?MarketingPosition
    {
        return $this->marketingPosition;
    }

    public function setMarketingPosition(?MarketingPosition $marketingPosition): self
    {
        $this->marketingPosition = $marketingPosition;

        return $this;
    }

    /**
     * @return Collection<int, ProductPriceHistory>
     */
    public function getProductPriceHistories(): Collection
    {
        return $this->productPriceHistories;
    }

    public function addProductPriceHistory(ProductPriceHistory $productPriceHistory): static
    {
        if (!$this->productPriceHistories->contains($productPriceHistory)) {
            $this->productPriceHistories->add($productPriceHistory);
            $productPriceHistory->setProduct($this);
        }

        return $this;
    }

    public function removeProductPriceHistory(ProductPriceHistory $productPriceHistory): static
    {
        if ($this->productPriceHistories->removeElement($productPriceHistory)) {
            // set the owning side to null (unless already changed)
            if ($productPriceHistory->getProduct() === $this) {
                $productPriceHistory->setProduct(null);
            }
        }

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(Stock $stock): static
    {
        // set the owning side of the relation if necessary
        if ($stock->getProduct() !== $this) {
            $stock->setProduct($this);
        }

        $this->stock = $stock;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
