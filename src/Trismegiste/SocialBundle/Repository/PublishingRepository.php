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
 * This is not a decorator of RepositoryInterface because we
 * try to avoid dumb repositories as well as dumb entities, only with methods
 * with business relevance. Plus, security concerns will break Liskov principle
 */
class PublishingRepository implements PublishingRepositoryInterface
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
     * @inheritdoc
     */
    public function findLastEntries($offset = 0, $limit = 20, \ArrayIterator $author = null)
    {
        $docFilter = $this->aliasFilter;
        if (!is_null($author)) {
            $filter = [];
            foreach ($author as $obj) {
                $filter[] = $obj->getNickname();
            }
            $docFilter['owner.nickname'] = ['$in' => $filter];
        }

        return $this->repository
                        ->find($docFilter)
                        ->limit($limit)
                        ->offset($offset)
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

    public function findWallEntries(Netizen $wallUser, $wallFilter, $offset = 0, $limit = 20)
    {
        switch ($wallFilter) {

            case 'self':
                $filterAuthor = new \ArrayIterator([$wallUser->getAuthor()]);
                break;

            case 'following':
                $filterAuthor = $wallUser->getFollowingIterator();
                break;

            case 'follower':
                $filterAuthor = $wallUser->getFollowerIterator();
                break;

            case 'friend':
                $filterAuthor = $wallUser->getFriendIterator();
                break;

            case 'all':
                $filterAuthor = null;
                break;

            default:
                throw new \InvalidArgumentException("$wallFilter is not valid filter");
        }

        return $this->repository->findLastEntries($offset, $limit, $filterAuthor);
    }

}