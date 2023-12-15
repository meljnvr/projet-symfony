<?php

namespace App\Security\Voter;

use App\Entity\Ad;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AdVoter extends Voter
{
    public const EDIT = 'AD_EDIT';
    public const DELETE = 'AD_DELETE';

    private $decisionManager;
    private $authorizationChecker;

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Ad;
    }
    /** 
     * @param Ad $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Vérifiez si l'utilisateur a le rôle admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifiez si l'utilisateur est l'auteur de l'annonce
        if ($subject->getUser() === $token->getUser()) {
            return true;
        }

        return false;
    }
}
