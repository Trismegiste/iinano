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
        $target = $this->getLoggedUser()->getUsername();

        return $this->repository->find([
                            MapAlias::CLASS_KEY => $this->classKey,
                            'target.nickname' => $target,
                            'read' => !$unread
                        ])
                        ->offset($offset)
                        ->sort(['sentAt' => -1]);
    }

    public function findAllSent($offset = 0, $unread = true)
    {
        $source = $this->getLoggedUser()->getUsername();

        return $this->repository->find([
                            MapAlias::CLASS_KEY => $this->classKey,
                            'source.nickname' => $source,
                            'read' => !$unread
                        ])
                        ->offset($offset)
                        ->sort(['sentAt' => -1]);
    }

    /**
     * Creates a new private message from the current user to a given author
     *
     * @param AuthorInterface $target
     *
     * @return PrivateMessage
     *
     * @throws AccessDeniedException
     */
    public function createNewMessageTo(AuthorInterface $target)
    {
        if (!$this->security->isGranted('LISTENER', $target)) {
            throw new AccessDeniedException("Cannot send a message to a user who does not follow you");
        }
        $source = $this->getLoggedUser();

        return new PrivateMessage($source->getAuthor(), $target);
    }

    public function persist(PrivateMessage $msg)
    {
        $this->repository->persist($msg);
    }

    /**
     * Returns the current logged netizen in session
     *
     * @return \Trismegiste\SocialBundle\Security\Netizen
     *
     * @throws AccessDeniedException if not logged
     */
    protected function getLoggedUser()
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Not logged');
        }

        return $this->security->getToken()->getUser();
    }

    public function persistAsRead($pk)
    {
        $pm = $this->repository->findByPk($pk);
        if (!$pm instanceof PrivateMessage) {
            throw new \LogicException("$pk is not a Private message");
        }

        if ($pm->getTarget() != $this->getLoggedUser()->getAuthor()) {
            throw new AccessDeniedException("You are not the receipient of this message");
        }

        $pm->markAsRead();
        $this->repository->persist($pm);
    }

    public function delete($pk)
    {
        $pm = $this->repository->findByPk($pk);
        if (!$pm instanceof PrivateMessage) {
            throw new \LogicException("$pk is not a Private message");
        }

        if ($pm->getSender() != $this->getLoggedUser()->getAuthor()) {
            throw new AccessDeniedException("You are not the sender of this message");
        }

        $pm->markAsRead(); // @todo the message is only mark as read: perhaps deleting is a better idea ?
        $this->repository->persist($pm);
    }

}
