<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;

/**
 * UserTransformer is a ...
 */
class UserTransformer implements DataTransformerInterface
{

    /** @var \Iterator */
    protected $nickname;

    /** @var Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface */
    protected $repository;

    public function __construct(NetizenRepositoryInterface $repo, \Iterator $choice)
    {
        $this->repository = $repo;
        $this->nickname = $choice;
    }

    public function reverseTransform($value)
    {
        if (null !== $value && !is_scalar($value)) {
            throw new TransformationFailedException('Expected a scalar.');
        }

        if (!$this->existsNickname($value)) {
            throw new TransformationFailedException("$value is not a valid user");
        }

        $found = $this->repository->findByNickname($value);
        if (is_null($found)) {
            throw new TransformationFailedException("$value is an unknown user");
        }

        return $found;
    }

    public function transform($user)
    {
        if (is_null($user)) {
            return null;
        }

        if (!$user instanceof \Trismegiste\SocialBundle\Security\Netizen) {
            throw new TransformationFailedException("Cannot transform from other class than Netizen");
        }

        if (!$this->existsNickname($user->getUsername())) {
            throw new TransformationFailedException($user->getUsername() . " is not valid user");
        }

        return $user->getUsername();
    }

    private function existsNickname($nick)
    {
        foreach ($this->nickname as $key => $dummy) {
            if ($key === $nick) {
                return true;
            }
        }

        return false;
    }

}
