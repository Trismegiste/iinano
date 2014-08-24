<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;

/**
 * NetizenRepository is a repository for Netizen
 * 
 * @todo Is this a decorator ( ie implementing RepositoryInterface ) ?
 */
class NetizenRepository implements NetizenRepositoryInterface
{

    protected $repository;

    public function __construct(RepositoryInterface $repo)
    {
        $this->repository = $repo;
    }

    public function findByNickname($nick)
    {
        
    }

}