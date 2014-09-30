<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;

/**
 * AuthorTransformer is a datatransformer from Author to nickname string and vice-versa
 */
class AuthorTransformer implements DataTransformerInterface
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

        return $found->getAuthor();
    }

    public function transform($user)
    {
        if (is_null($user)) {
            return null;
        }

        if (!$user instanceof \Trismegiste\Socialist\AuthorInterface) {
            throw new TransformationFailedException("Cannot transform from other class than AuthorInterface");
        }

        if (!$this->existsNickname($user->getNickname())) {
            throw new TransformationFailedException($user->getNickname() . " is not valid author");
        }

        return $user->getNickname();
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
