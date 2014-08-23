<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;
use Trismegiste\Yuurei\Persistence\Persistable;

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
    protected $aliasFilter;

    // @todo put alias in the configuration of this bundle (with validation)
    public function __construct(RepositoryInterface $repo, SecurityContextInterface $ctx, $aliases = [])
    {
        $this->security = $ctx;
        $this->repository = $repo;
        $this->aliasFilter = [MapAlias::CLASS_KEY => ['$in' => ['post']]]; // @todo EVIL
    }

    public function findLastEntries($limit = 20)
    {
        return $this->repository
                        ->find($this->aliasFilter)
                        ->limit($limit)
                        ->sort(['createdAt' => -1]);
    }

    public function persist(Persistable $doc)
    {
        $this->repository->persist($doc);
    }

    public function findByPk($pk)
    {
        return $this->repository->findByPk($pk);
    }

}