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
 *
 * @todo queries need indexing [target.nickname:1 , sentAt:-1]
 * @todo queries need indexing [source.nickname:1 , sentAt:-1]
 */
class PrivateMessageRepository extends SecuredContentProvider
{

    protected $classKey;

    public function __construct(RepositoryInterface $repo, SecurityContextInterface $ctx, $alias)
    {
        parent::__construct($repo, $ctx);
        $this->classKey = $alias;
    }

    public function findAllReceived($offset = 0, $unread = true)
    {
        return $this->repository->find([
                            MapAlias::CLASS_KEY => $this->classKey,
                            'target.nickname' => $this->getNickname(), // @todo mongo sparse index for this key
                            'read' => !$unread
                        ])
                        ->offset($offset)
                        ->sort(['sentAt' => -1]);
    }

    public function findAllSent($offset = 0, $unread = true)
    {
        return $this->repository->find([
                            MapAlias::CLASS_KEY => $this->classKey,
                            'source.nickname' => $this->getNickname(), // @todo mongo sparse index for this key
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

        return new PrivateMessage($this->getAuthor(), $target);
    }

    public function persist(PrivateMessage $msg)
    {
        // @todo Common behavior with OWNER voter => common interface "Ownable" with "::getOwnerAuthor()" ?
        // not critical because users don't see this access right within a browser
        // (unlike Edit/Delete for example)
        if (!$msg->getSender()->isEqual($this->getAuthor())) {
            throw new AccessDeniedException("You cannot save this message because you're not the sender");
        }

        $this->repository->persist($msg);
    }

    public function persistAsRead($pk)
    {
        $pm = $this->repository->findByPk($pk);
        if (!$pm instanceof PrivateMessage) {
            throw new \LogicException("$pk is not a Private message");
        }

        if (!$pm->getTarget()->isEqual($this->getAuthor())) {
            throw new AccessDeniedException("You are not the receipient of this message");
        }

        $pm->markAsRead();
        $this->repository->persist($pm);
    }

}
