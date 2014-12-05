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
     */
    public function isValid();

    /**
     * Gets the date of purchase for this entrance access
     */
    public function getPurchasedAt();
}
