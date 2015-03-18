<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Trismegiste\SocialBundle\Security\Netizen;

/**
 * TicketVoter is a voter to vote if a user has a valid ticket for entrance
 */
class TicketVoter implements VoterInterface
{

    public function supportsAttribute($attribute)
    {
        return 'VALID_TICKET' === $attribute;
    }

    public function supportsClass($fqcn)
    {

    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException('Only one attribute is allowed for TicketVoter');
        }

        // set the attribute to check against
        $attribute = $attributes[0];
        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof Netizen) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($user->hasValidTicket()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        // if everything else fails:
        return VoterInterface::ACCESS_DENIED;
    }

}
