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

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {

    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->cred->getPassword();
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return $this->cred->getSalt();
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->author->getNickname();
    }

    /**
     * Sets the strategy for credentials
     *
     * @param \Trismegiste\SocialBundle\Security\Credential\Strategy $strat
     */
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

    /**
     * Gets the profile from this user
     *
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Sets the profiles for this user
     *
     * @param \Trismegiste\SocialBundle\Security\Profile $pr
     */
    public function setProfile(Profile $pr)
    {
        $this->profile = $pr;
    }

    /**
     * Add (append) a role to this user
     *
     * @param string $str
     */
    public function addRole($str)
    {
        if (!in_array($str, $this->roles)) {
            $this->roles[] = $str;
        }
    }

    /**
     * Overrides the group ROLE for this user
     *
     * @param string $str
     */
    public function setGroup($str)
    {
        $this->roles = [$str];
    }

    /**
     * Is this user still has a valid ticket for accessing to the app
     *
     * @return boolean
     */
    public function hasValidTicket()
    {
        return true;
    }

}
