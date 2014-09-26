<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Socialist\PrivateMessage;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;
use Trismegiste\Socialist\AuthorInterface;
use \Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * PrivateMessageRepository is a repository for PrivateMessage
 */
class PrivateMessageRepository
{

    protected $classKey;

    /** @var Trismegiste\Yuurei\Persistence\RepositoryInterface */
    protected $repository;

    /** \Symfony\Component\Security\Core\SecurityContextInterface */
    protected $security;

    public function __construct(RepositoryInterface $repo, SecurityContextInterface $ctx, $alias)
    {
        $this->repository = $repo;
        $this->security = $ctx;
        $this->classKey = $alias;
    }

    public function findAllReceived($offset = 0, $unread = true)
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Not logged');
        }

        $target = $this->security->getToken()->getUsername();
        return $this->repository->find([
                    MapAlias::CLASS_KEY => $this->classKey,
                    'target.nickname' => $target,
                    'read' => !$unread
                ])->offset($offset);
    }

    public function createNewMessageTo(AuthorInterface $target)
    {
        if (!$this->security->isGranted('LISTENER', $target)) {
            throw new AccessDeniedException("Cannot send a message to a user who does not follow you");
        }

        return new PrivateMessage($source->getAuthor(), $target);
    }

}
