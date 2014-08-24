<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;
use Trismegiste\Socialist\Publishing;

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

    public function __construct(RepositoryInterface $repo, SecurityContextInterface $ctx, $aliases)
    {
        $this->security = $ctx;
        $this->repository = $repo;
        $this->aliasFilter = [MapAlias::CLASS_KEY => ['$in' => $aliases]];
    }

    /**
     * Retrieves an iterator on the last published entries
     * 
     * @param int $limit
     * 
     * @return \Trismegiste\Yuurei\Persistence\CollectionIterator
     */
    public function findLastEntries($limit = 20)
    {
        return $this->repository
                        ->find($this->aliasFilter)
                        ->limit($limit)
                        ->sort(['createdAt' => -1]);
    }

    /**
     * Persists a published content
     * 
     * @param \Trismegiste\Socialist\Publishing $doc
     */
    public function persist(Publishing $doc)
    {
        $this->repository->persist($doc);
    }

    /**
     * Returns a published content by its PK
     * 
     * @param string $pk
     * 
     * @return \Trismegiste\Socialist\Publishing
     */
    public function findByPk($pk)
    {
        return $this->repository->findByPk($pk);
    }

}