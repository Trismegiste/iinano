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

    /**
     * A strategy for authentication
     * @var Credential\Strategy
     */
    protected $cred;

    /**
     * @var Profile
     */
    protected $profile;

    /**
     * @var array of roles
     */
    protected $roles = [];

    public function eraseCredentials()
    {

    }

    public function getPassword()
    {
        return $this->cred->getPassword();
    }

    public function getRoles()
    {
        return $this->roles;
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

    public function addRole($str)
    {
        if (!in_array($str, $this->roles)) {
            $this->roles[] = $str;
        }
    }

    public function setGroup($str)
    {
        $this->roles = [$str];
    }

    public function hasValidTicket()
    {
        return false;
    }

}
