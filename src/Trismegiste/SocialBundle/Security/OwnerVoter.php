<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Trismegiste\Socialist\Content;
use Trismegiste\SocialBundle\Security\SocialUser;

/**
 * OwnerVoter is a voter to vote if a user has owning rights on a Content
 */
class OwnerVoter implements VoterInterface
{

    public function supportsAttribute($attribute)
    {
        return 'OWNER' === $attribute;
    }

    public function supportsClass($class)
    {
        return $class instanceof Content;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($this->supportsAttribute($attribute) && $this->supportsClass($object)) {
                $author = $token->getUser()->getAuthor();

                if (($token->getUser() instanceof SocialUser) && ($object->getAuthor() == $author)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
            }
        }

        return VoterInterface::ACCESS_DENIED;
    }

}