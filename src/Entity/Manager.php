<?php

namespace App\Entity;

use App\Repository\ManagerRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ManagerRepository::class)]
#[UniqueEntity('matricule', message: 'Ce matricule est déjà utilisé.')]
class Manager extends User
{

    #[ORM\Column(length: 255, unique: true)]
    // #[Assert\NotBlank(message: 'Le matricule est obligatoire.')]
    #[Assert\Length(
    min: 6,
    max: 10,
    minMessage: 'Le matricule doit contenir au moins {{ limit }} caractères.',
    maxMessage: 'Le matricule ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $matricule = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le département est obligatoire.')]
    #[Assert\Choice(
            choices: ['Direction', 'Marketing', 'Product', 'Logistic'],
            message: 'Le département "{{ value }}" n’est pas valide. Veuillez choisir parmi {{ choices }}.'
    )]
    private ?string $departement = null;

    #[ORM\Column()]
    #[Assert\NotNull]
    #[Assert\LessThanOrEqual('now', message: 'La date de création ne peut pas être dans le futur.')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

 

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): static
    {
        if ($this->matricule !== null) {
           throw new \LogicException('Le matricule ne peut pas être modifié une fois défini.');
       }
       
        $this->matricule = $matricule;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(string $departement): static
    {
        $this->departement = $departement;

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
}
