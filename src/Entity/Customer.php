<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
   

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le prenom est obligatoire.")]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private ?string $lastname = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "Le téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^0[1-9]\d{8}$/",
        message: "Le numéro de téléphone doit contenir 10 chiffres et commencer par un 0."
    )]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastVisitedAt = null;

    /**
     * @var Collection<int, Address>
     */
    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'customer', orphanRemoval: true)]
    private Collection $addresses;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'customer')]
    private Collection $orders;

    public function __construct()
    {
       
        $this->setRoles(['ROLE_CUSTOMER']);
        $this->createdAt = new \DateTimeImmutable();
        $this->lastVisitedAt = new \DateTimeImmutable();
        $this->addresses = new ArrayCollection();
        $this->orders = new ArrayCollection();
       
    }



    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

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

    public function getLastVisitedAt(): ?\DateTimeImmutable
    {
        return $this->lastVisitedAt;
    }

    public function setLastVisitedAt(\DateTimeImmutable $lastVisitedAt): static
    {
        $this->lastVisitedAt = $lastVisitedAt;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setCustomer($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getCustomer() === $this) {
                $address->setCustomer(null);
            }
        }

        return $this;
    }

    public function setPrimaryAddress(Address $newPrimaryAddress): void
    {
        foreach ($this->addresses as $address) {
            // Désactive l'ancienne adresse principale
            $address->setIsPrimary(false); 
        }
        $newPrimaryAddress->setIsPrimary(true);
    }

    public function getPrimaryAddress(): ?Address
    {
        foreach ($this->addresses as $address) {
            if ($address->isPrimary()) {
                return $address;
            }
        }
        return null;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCustomer($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }

    public function getPrimaryShippingAddress(): ?Address
{
    foreach ($this->addresses as $address) {
        if ($address->isPrimary() && $address->getType() === 'livraison') {
            return $address;
        }
    }
    return null;
}

   
}
