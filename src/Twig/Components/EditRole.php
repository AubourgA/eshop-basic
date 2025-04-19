<?php

namespace App\Twig\Components;

use App\Entity\Manager; // ou User si c'est l'entité utilisée
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;


#[AsLiveComponent]
class EditRole extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

  
    #[LiveProp(writable: ['roles.0'])]
    public Manager $manager;

    #[LiveProp]
    public bool $isEditing = false;

    
  

    #[LiveAction]
    public function activateEditing()
    {
        $this->isEditing = true;
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->validate();

       
        $entityManager->flush();

        $this->isEditing = false;
    }
}