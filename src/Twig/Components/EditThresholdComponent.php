<?php

namespace App\Twig\Components;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;


#[AsLiveComponent()]
class EditThresholdComponent extends AbstractController
{
    use DefaultActionTrait;
  

    
   #[LiveProp(writable: ['threshold'])]
    public Stock $stock;



    #[LiveProp(writable: true)]
    public bool $isEditing = false;


     #[LiveAction]
    public function activateEditing()
    {
        $this->isEditing = true;
    }


    #[LiveAction]
    public function save(EntityManagerInterface $em)
    {
        $em->flush();
        $this->isEditing = false;  
    }
}