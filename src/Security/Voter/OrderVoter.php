<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Contrôle d'accès pour les entités Order.
 * 
 * Ce voter gère les droits d'accès pour visualiser une commande individuelle (VIEW)
 * ou toutes les commandes (VIEW_ALL).
 * 
 * - VIEW : accès autorisé au client propriétaire de la commande ou aux utilisateurs ayant le rôle ROLE_PRODUCT.
 * - VIEW_ALL : accès réservé aux utilisateurs ayant le rôle ROLE_PRODUCT ou SUPERIEUR.
 */
final class OrderVoter extends Voter
{
   
    public const VIEW = 'VIEW';
    public const VIEW_ALL = 'VIEW_ALL';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager,
        ) 
        {
        }
        
    protected function supports(string $attribute, mixed $subject): bool
    {
        
        return  in_array($attribute, [self::VIEW, self::VIEW_ALL])
            && $subject instanceof \App\Entity\Order;
    }

    protected function voteOnAttribute(string $attribute, 
                                        mixed $subject, 
                                        TokenInterface $token,
                                        ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        return match ($attribute) {
            self::VIEW_ALL => $this->accessDecisionManager->decide($token, ['ROLE_PRODUCT']),
            self::VIEW => $this->canView($subject, $user) || $this->accessDecisionManager->decide($token, ['ROLE_PRODUCT']),
            default => false,
        };
    }

    /**
     * Vérifie si l'utilisateur peut voir la commande.
     * 
     * L'utilisateur peut voir la commande s'il est le client propriétaire.
     * 
     * @param Order $order La commande à vérifier
     * @param UserInterface $user L'utilisateur courant
     * 
     * @return bool True si le client est propriétaire de la commande, false sinon
     */
    private function canView(Order $order, User $user): bool
    {
        return $order->getCustomer() === $user;
    }
}
