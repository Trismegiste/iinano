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

    /**
     * @var Profile
     */
    protected $profile;

    public function eraseCredentials()
    {
        
    }

    public function getPassword()
    {
        return $this->cred->getPassword();
    }

    public function getRoles()
    {
        // @todo not implemented yet
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        return $this->cred->getSalt();
    }

    public function getUsername()
    {
        return $this->author->getNickname();
    }

    public function setCredential(Credential\Strategy $strat)
    {
        $this->cred = $strat;
    }

    /**
     * For further support in authenticationProvider or security listener
     * 
     * @return string fqcn
     */
    public function getCredentialType()
    {
        // @todo perhaps a better idea to return an "abstract key type" of the credential
        return get_class($this->cred);
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile(Profile $pr)
    {
        $this->profile = $pr;
    }

}