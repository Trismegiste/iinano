<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * PublishingRepository is a business repository for subclasses of Publishing
 * 
 * This is a wrapper around a RepositoryInterface with SecurityContext
 * @todo Is this a decorator ( ie implementing RepositoryInterface ) ?
 * Perhaps not because security issue will tend to break liskov on persist/find/restore...
 * Try to avoid dumb repositories as well as dumb entities, only with methods
 * with business relevance.
 * Maybe an interface for decoupling will be a good idea.
 */
class PublishingRepository
{

    protected $repository;
    protected $security;

    public function __construct(RepositoryInterface $repo, SecurityContextInterface $ctx)
    {
        $this->security = $ctx;
        $this->repository = $repo;
    }

    public function findLast($limit = 20)
    {
        return $this->repository
                        ->find()
                        ->limit($limit)
                        ->sort(['createdAt' => false]);
    }

}