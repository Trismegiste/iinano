<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Trismegiste\SocialBundle\Security\Netizen;

/**
 * OwnerVoter is a voter to vote if a user has owning rights on a Content
 */
class OwnerVoter implements VoterInterface
{

    public function supportsAttribute($attribute)
    {
        return 'OWNER' === $attribute;
    }

    public function supportsClass($fqcn)
    {
        return is_subclass_of($fqcn, 'Trismegiste\Socialist\Content');
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException('Only one attribute is allowed for OwnerVoter');
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

        // compare the two Authors on nickname (avatar can change)
        if ($object->getAuthor()->getNickname() == $user->getAuthor()->getNickname()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        // if everything else fails:
        return VoterInterface::ACCESS_DENIED;
    }

}