<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * PublishingRepository is a business repository for subclasses of Publishing
 * 
 * This is a wrapper around a RepositoryInterface with SecurityContext
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