<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Trismegiste\Socialist\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Trismegiste\SocialBundle\Ticket\EntranceAccess;
use Trismegiste\SocialBundle\Ticket\InvalidTicketException;

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
     * @var string one role in the hierarchy roles security
     */
    protected $roleGroup;

    /**
     * @var array of ticket
     */
    protected $ticket = [];

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
        return [$this->roleGroup];
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
     * Get the role of this user
     *
     * @return string the role group
     */
    public function getGroup()
    {
        return $this->roleGroup;
    }

    /**
     * Overrides the group ROLE for this user
     *
     * @param string $str
     */
    public function setGroup($str)
    {
        $this->roleGroup = $str;
    }

    /**
     * Is this user still has a valid ticket for accessing to the app
     *
     * @return boolean
     */
    public function hasValidTicket()
    {
        $last = $this->getLastTicket();

        return !is_null($last) && $last->isValid();
    }

    /**
     * Add a valid ticket to this user with an invalid ticket
     *
     * @param EntranceAccess $ticket
     *
     * @throws InvalidTicketException
     */
    public function addTicket(EntranceAccess $ticket)
    {
        // we add the ticket only if it is valid...
        if (!$ticket->isValid()) {
            throw new InvalidTicketException('The ticket is not valid');
        }
        // ... and the last current ticket is not
        if ($this->hasValidTicket()) {
            throw new InvalidTicketException('The user has currently a valid ticket');
        }

        array_unshift($this->ticket, $ticket);
    }

    /**
     * Returns the last added ticket
     *
     * @return EntranceAccess|null
     */
    public function getLastTicket()
    {
        return count($this->ticket) ? $this->ticket[0] : null;
    }

    /**
     * Get an interator on tickets
     *
     * @return \ArrayIterator
     */
    public function getTicketIterator()
    {
        return new \ArrayIterator($this->ticket);
    }

}
