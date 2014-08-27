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

    public function getCredential()
    {
        return $this->password;
    }

    public function __construct($pwd)
    {
        $this->password = $pwd;
    }

}