<?php

namespace App\Controller\API\Customer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilAPIController extends AbstractController
{
    #[Route('/api/update_phone', name: 'api_update_phone', methods:["POST"])]
    public function update_phone(Request $request, 
                                EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(),true);
        $newPhone = $data['phone'] ?? null;

        if (!$newPhone) {
            return $this->json(['error' => 'Numéro manquant'], 400);
        }

        $user->setPhone($newPhone);
       
        $em->flush();
     
        return $this->json(["succes"=>true, "phone"=>$newPhone]);
    }
}
