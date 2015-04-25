<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;

/**
 * AbuseReport is a repository for compiling and listing abusive or spam report
 * on Content (Commentary and Publishing subclasses)
 */
class AbuseReport
{

    /** @var RepositoryInterface */
    protected $repository;

    /** @var \MongoCollection */
    protected $collection;

    /** @var array */
    protected $pubAlias;

    /**
     * Ctor
     *
     * @param RepositoryInterface $repo repository of Content
     * @param array $aliases an array of aliases for Publishing subclasses
     */
    public function __construct(RepositoryInterface $repo, array $aliases, \MongoCollection $coll)
    {
        $this->repository = $repo;
        $this->pubAlias = $aliases;
        $this->collection = $coll;
    }

    /**
     * Retrieves an iterator on a list of abusive Publishing (root) entities
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \Trismegiste\Yuurei\Persistence\CollectionIterator
     */
    public function findMostReportedPublish($offset = 0, $limit = 20)
    {
        return $this->repository->find([
                            'abusiveCount' => ['$gt' => 0]
                        ])
                        ->sort(['abusiveCount' => -1])
                        ->offset($offset)
                        ->limit($limit);
    }

    /**
     * Retrieves an iterator on a list of abusive Commentary
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \Trismegiste\Yuurei\Persistence\CollectionIterator
     */
    public function findMostReportedCommentary($offset = 0, $limit = 20)
    {
        return $this->collection->aggregateCursor([
                    ['$match' => [
                            'commentary.0' => ['$exists' => true],
                            'commentary' => ['$elemMatch' => ['abusiveCount' => ['$gt' => 0]]]
                        ]
                    ], // I love arrays
                    ['$unwind' => '$commentary'],
                    ['$match' => ['commentary.abusiveCount' => ['$gt' => 0]]],
                    ['$project' => ['commentary' => true]]
        ]);
    }

}
