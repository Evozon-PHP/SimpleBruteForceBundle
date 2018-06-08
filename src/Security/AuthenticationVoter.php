<?php

namespace EvozonPhp\SimpleBruteForceBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Regular authentication voter.
 *
 * @author Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
class AuthenticationVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        return VoterInterface::ACCESS_GRANTED;
    }
}
