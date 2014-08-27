<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Trismegiste\Socialist\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Trismegiste\Socialist\AuthorInterface;

/**
 * Netizen is a User with features of login, security 
 * and connection stuff with external provider
 */
class Netizen extends User implements UserInterface
{

    /**
     * A strategy for authentication
     * @var Credential\Strategy 
     */
    protected $cred;

    public function eraseCredentials()
    {
        
    }

    public function getPassword()
    {
        return $this->cred->getCredential();
    }

    public function getRoles()
    {
        // @todo not implemented yet
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

    public function __construct(AuthorInterface $author, Credential\Strategy $strat)
    {
        parent::__construct($author);
        $this->cred = $strat;
    }

    /**
     * For further support in authenticationProvider or security listener
     * 
     * @return string fqcn
     */
    public function getCredentialType()
    {
        return get_class($this->cred);
    }

}