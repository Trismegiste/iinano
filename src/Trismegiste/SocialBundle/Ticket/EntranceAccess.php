<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * EntranceAccess is a contract for an entrance access to this app
 */
interface EntranceAccess
{

    /**
     * Is this entrance access still valid ?
     *
     * @return bool
     */
    public function isValid(\DateTime $now = null);

    /**
     * Gets the date of purchase for this entrance access
     *
     * @return \DateTime
     */
    public function getPurchasedAt();

    /**
     * Gets the expiration date
     *
     * @return \DateTime
     */
    public function getExpiredAt();

    /**
     * Gets a human readable label for this object
     */
    public function getTitle();
}
