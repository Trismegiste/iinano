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

    /**
     * From key to object
     *
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (null !== $value && !is_scalar($value)) {
            throw new TransformationFailedException('Expected a scalar.');
        }

        if (!$this->existsNickname($value)) {
            throw new TransformationFailedException("$value is an invalid choice");
        }

        $found = $this->repository->findByNickname($value);
        if (is_null($found)) {
            throw new TransformationFailedException("Author $value is not found");
        }

        return $found->getAuthor();
    }

    /**
     * From object to key
     *
     * {@inheritDoc}
     */
    public function transform($user)
    {
        if (is_null($user)) {
            return null;
        }

        if (!is_object($user)) {
            throw new TransformationFailedException("Data value is not an object");
        }

        if (!$user instanceof \Trismegiste\Socialist\AuthorInterface) {
            throw new TransformationFailedException("Cannot transform from other class than AuthorInterface");
        }

        if (!$this->existsNickname($user->getNickname())) {
            throw new TransformationFailedException('Author ' . $user->getNickname() . " cannot be chosen");
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
