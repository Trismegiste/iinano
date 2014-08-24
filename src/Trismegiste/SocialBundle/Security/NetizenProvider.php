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
        return new Netizen(new \Trismegiste\Socialist\Author($username));
    }

    public function refreshUser(UserInterface $user)
    {
        return new Netizen(new \Trismegiste\Socialist\Author($user->getUsername()));
    }

    public function supportsClass($class)
    {
        return $class === __NAMESPACE__ . '\Netizen';
    }

}