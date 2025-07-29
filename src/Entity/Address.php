<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniquePrimaryAddress;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[UniquePrimaryAddress]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La rue ne peut pas être vide.")]
    private ?string $street = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "La ville est obligatoire.")]
    private ?string $city = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le code postal est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^\d{4,5}(-\d{3,4})?$/",
        message: "Le code postal doit être valide."
    )]
    private ?string $postalCode = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le pays est obligatoire.")]
    private ?string $country = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le type d'adresse est obligatoire.")]
    #[Assert\Choice(
        choices: ['livraison', 'facturation'],
        message: "Le type d'adresse doit être 'facturation' ou 'livraison'"
    )]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "L'indication de l'adresse principale est obligatoire.")]
    private ?bool $isPrimary = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'billingAddress')]
    private Collection $billingOrders;

     /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'shippingAddress')]
    private Collection $shippingOrders;


    public function __construct()
    {
        $this->billingOrders = new ArrayCollection();
        $this->shippingOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = ucfirst(strtolower($city));

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = strtoupper($country);

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isPrimary(): ?bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): static
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

 /**
 * @return Collection<int, Order>
 */
public function getBillingOrders(): Collection
{
    return $this->billingOrders;
}

/**
 * @return Collection<int, Order>
 */
public function getShippingOrders(): Collection
{
    return $this->shippingOrders;
}

public function addBillingOrder(Order $order): static
{
    if (!$this->billingOrders->contains($order)) {
        $this->billingOrders->add($order);
        $order->setBillingAddress($this);
    }

    return $this;
}

public function removeBillingOrder(Order $order): static
{
    if ($this->billingOrders->removeElement($order)) {
        // set the owning side to null (unless already changed)
        if ($order->getBillingAddress() === $this) {
            $order->setBillingAddress(null);
        }
    }

    return $this;
}

public function addShippingOrder(Order $order): static
{
    if (!$this->shippingOrders->contains($order)) {
        $this->shippingOrders->add($order);
        $order->setShippingAddress($this);
    }

    return $this;
}

public function removeShippingOrder(Order $order): static
{
    if ($this->shippingOrders->removeElement($order)) {
        // set the owning side to null (unless already changed)
        if ($order->getShippingAddress() === $this) {
            $order->setShippingAddress(null);
        }
    }

    return $this;
}
}
