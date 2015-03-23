<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Trismegiste\Socialist\Content;

/**
 * SecuredContentProvider is an abstract repository for fully authenticated user
 */
abstract class SecuredContentProvider
{

    /** @var Trismegiste\Yuurei\Persistence\RepositoryInterface */
    protected $repository;

    /** \Symfony\Component\Security\Core\SecurityContextInterface */
    protected $security;

    public function __construct(RepositoryInterface $repo, SecurityContextInterface $ctx)
    {
        $this->repository = $repo;
        $this->security = $ctx;
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
        if (!$this->security->isGranted('VALID_TICKET')) {
            throw new AccessDeniedException('Not logged');
        }

        return $this->security->getToken()->getUser();
    }

    /**
     * Get the current logged author
     *
     * @return \Trismegiste\Socialist\AuthorInterface
     */
    protected function getAuthor()
    {
        return $this->getLoggedUser()->getAuthor();
    }

    /**
     * Get the current logged user nickname
     *
     * @return string
     */
    protected function getNickname()
    {
        return $this->getLoggedUser()->getUsername();
    }

    /**
     * Check if the logged user is the owner of a given content
     *
     * @param Content $post the content to check
     *
     * @throws AccessDeniedException if the user cannot
     */
    protected function assertOwningRight(Content $post)
    {
        if (!$this->security->isGranted('OWNER', $post)) {
            throw new AccessDeniedException('Unauthorised access to this content');
        }
    }

}
