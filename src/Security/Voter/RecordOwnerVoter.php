<?php

namespace App\Security\Voter;

use App\Entity\Appointments;
use App\Entity\Orders;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

final class RecordOwnerVoter extends Voter
{
    public const EDIT = 'RECORD_EDIT';
    public const DELETE = 'RECORD_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && ($subject instanceof Orders || $subject instanceof Appointments);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token,?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                // logic to determine if the user can EDIT
                // return true or false
                if (!$user instanceof User) {
                    return false;
                }

                $owner = $subject->getCreatedBy();

                if (null !== $owner) {
                    return $owner->getId() === $user->getId();
                }

                return null !== $subject->getCustomer();
        }

        return false;
    }
}
