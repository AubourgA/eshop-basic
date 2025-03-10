<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;


#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
   

    #[ORM\Column(length: 100)]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    private ?string $lastname = null;

    #[ORM\Column(length: 10)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastVisitedAt = null;

    public function __construct()
    {
       
        $this->setRoles(['ROLE_CUSTOMER']);
        $this->createdAt = new \DateTimeImmutable();
        $this->lastVisitedAt = new \DateTimeImmutable();
       
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

   
}
