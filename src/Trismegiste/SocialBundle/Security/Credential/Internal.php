<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security\Credential;

/**
 * Internal is an internal strategy for authentication
 */
class Internal implements Strategy
{

    private $password;
    private $salt;

    public function getPassword()
    {
        return $this->password;
    }

    public function __construct($pwd, $salt)
    {
        $this->password = $pwd;
        $this->salt = $salt;
    }

    public function getSalt()
    {
        return $this->salt;
    }

}