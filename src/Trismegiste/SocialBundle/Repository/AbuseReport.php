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

    /** @var array */
    protected $pubAlias;

    /**
     * Ctor
     *
     * @param RepositoryInterface $repo repository of Content
     * @param array $aliases an array of aliases for Publishing subclasses
     */
    public function __construct(RepositoryInterface $repo, array $aliases)
    {
        $this->repository = $repo;
        $this->pubAlias = $aliases;
    }

    /**
     * Retrieves an iterator on a compiled list on abuse reports
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \MongoCursor
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

}
