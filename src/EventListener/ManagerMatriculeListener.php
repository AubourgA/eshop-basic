<?php

namespace App\EventListener;

use App\Entity\Manager;
use App\Repository\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;

#[AsDoctrineListener(event: Events::prePersist)]
class ManagerMatriculeListener
{
    public function __construct(private ManagerRepository $managerRepository)
    {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Manager) {
            return;
        }

        // Si le matricule est déjà défini, on ne touche pas
        if ($entity->getMatricule()) {
            return;
        }

        // Génération d’un matricule unique à 6 chiffres
        do {
            $matricule = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while ($this->managerRepository->findOneBy(['matricule' => $matricule]));

        $entity->setMatricule($matricule);
    }
}