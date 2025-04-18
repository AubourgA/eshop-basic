<?php


namespace App\Twig\Components;

use App\Entity\Manager;
use App\Form\ManagerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
class CreateManagerForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?Manager $initialFormData = null;

    #[LiveProp(writable: true)]
    public ?string $plainPassword = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ManagerType::class, $this->initialFormData, [
            'plainPassword' => $this->plainPassword
        ] );
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->submitForm();

        $manager = $this->getForm()->getData();
      
        $hashedPassword = $hasher->hashPassword($manager, $this->plainPassword);
        $manager->setPassword($hashedPassword);
        $entityManager->persist($manager);
        $entityManager->flush();

        $this->resetForm();

    }
}
