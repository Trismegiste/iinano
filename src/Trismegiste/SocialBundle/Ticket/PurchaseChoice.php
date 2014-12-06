<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * PurchaseChoice is a contract for different systems for acquiring Ticket
 *
 * @todo : is it this contract which is Persistable or is it the implementation ? dunno
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
