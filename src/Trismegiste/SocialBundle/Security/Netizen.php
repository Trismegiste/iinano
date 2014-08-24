<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Trismegiste\Socialist\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Netizen is a User with features of login, security 
 * and connection stuff with external provider
 */
class Netizen extends User implements UserInterface
{

    // Listing of strategies for connection
    protected $connect = [];

    public function eraseCredentials()
    {
        
    }

    public function getPassword()
    {
        return 'aaaa';
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->author->getNickname();
    }

}