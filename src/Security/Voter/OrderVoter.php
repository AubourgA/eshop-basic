<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

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

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        
        return match ($attribute) {
            self::VIEW_ALL => $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']),
            self::VIEW => $this->canView($subject, $user) || $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']),
            default => false,
        };
    }

    private function canView(Order $order, User $user): bool
    {
        return $order->getCustomer() === $user;
    }
}
