<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;

/**
 * NetizenProvider is a provider of symfony user based on Socialist User
 */
class NetizenProvider implements UserProviderInterface
{

    protected $socialRepository;

    public function __construct(NetizenRepositoryInterface $repo)
    {
        $this->socialRepository = $repo;
    }

    public function loadUserByUsername($username)
    {
        return $this->socialRepository->create($username);
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->socialRepository->create($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === __NAMESPACE__ . '\Netizen';
    }

}