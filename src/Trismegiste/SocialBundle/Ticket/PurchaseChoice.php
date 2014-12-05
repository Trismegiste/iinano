<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * PurchaseChoice is a contract for different systems for acquiring Ticket
 */
interface PurchaseChoice
{

    /**
     * Gets the duration property of this PurchaseChoice
     *
     * @return \DateInterval
     */
    public function getDuration();
}
