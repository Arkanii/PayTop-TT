<?php

// https://api-platform.com/docs/core/security/#hooking-custom-permission-checks-using-voters

namespace App\Security\Voter;

use App\Entity\Customer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CustomerVoter extends Voter
{
    public function __construct(private Security $security)
    {
    }

    protected function supports($attribute, $subject): bool
    {
        $supportsAttribute = $attribute === 'CUSTOMER_CREATE';
        $supportsSubject = $subject instanceof Customer;

        return $supportsAttribute && $supportsSubject;
    }

    /**
     * @param string $attribute
     * @param Customer $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return !(($attribute === 'CUSTOMER_CREATE') && $this->security->isGranted("ROLE_ADMIN"));
    }
}
