<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Trismegiste\SocialBundle\Security\Netizen;

/**
 * ListenerVoter is a voter to vote if a user has the rights to send some content
 * toward another user (a listener)
 */
class ListenerVoter implements VoterInterface
{

    const ROLE_SEND_TO_LISTENER = 'LISTENER';

    public function supportsAttribute($attribute)
    {
        return self::ROLE_SEND_TO_LISTENER === $attribute;
    }

    public function supportsClass($fqcn)
    {
        return is_subclass_of($fqcn, 'Trismegiste\Socialist\AuthorInterface');
    }

    public function vote(TokenInterface $token, $target, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($target))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException('Only one attribute is allowed for ListenerVoter');
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

        // check if target following the current user (he is a follower a.k.a a listener)
        if ($user->followerExists($target->getNickname())) {
            return VoterInterface::ACCESS_GRANTED;
        }

        // if everything else fails:
        return VoterInterface::ACCESS_DENIED;
    }

}
