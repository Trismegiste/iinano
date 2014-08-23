<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Trismegiste\Yuurei\Persistence\RepositoryInterface;

/**
 * SocialUserProvider is a provider of symfony user based on Socialist User
 */
class SocialUserProvider implements UserProviderInterface
{

    protected $socialRepository;

    public function __construct(RepositoryInterface $repo)
    {
        $this->socialRepository = $repo;
    }

    public function loadUserByUsername($username)
    {
        return new SocialUser(new \Trismegiste\Socialist\Author($username));
    }

    public function refreshUser(UserInterface $user)
    {
        return new SocialUser(new \Trismegiste\Socialist\Author($user->getUsername()));
    }

    public function supportsClass($class)
    {
        return $class === __NAMESPACE__ . '\SocialUser';
    }

}