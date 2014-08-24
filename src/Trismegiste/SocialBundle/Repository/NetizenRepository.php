<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Socialist\Author;

/**
 * NetizenRepository is a repository for Netizen (and also Author)
 * 
 * @todo Is this a decorator ( ie implementing RepositoryInterface ) ?
 */
class NetizenRepository implements NetizenRepositoryInterface
{

    protected $repository;
    protected $classAlias;

    public function __construct(RepositoryInterface $repo, $alias)
    {
        $this->repository = $repo;
        $this->classAlias = $alias;
    }

    public function findByNickname($nick)
    {
        $obj = $this->repository->findOne([
            MapAlias::CLASS_KEY => $this->classAlias,
            'author.nickname' => $nick
        ]);

        return $obj;
    }

    public function create($nick)
    {
        // @todo check on unique nickname here ? => yes
        return new Netizen(new Author($nick));
    }

    public function persist(Netizen $obj)
    {
        $this->repository->persist($obj);
    }

}