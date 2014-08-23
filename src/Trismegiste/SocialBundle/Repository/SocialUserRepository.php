<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;

/**
 * SocialUserRepository is a repository for SocialUser
 * 
 * @todo Is this a decorator ( ie implementing RepositoryInterface ) ?
 */
class SocialUserRepository
{

    protected $repository;

    public function __construct(RepositoryInterface $repo)
    {
        $this->repository = $repo;
    }

    public function findByNickname()
    {
        
    }

}