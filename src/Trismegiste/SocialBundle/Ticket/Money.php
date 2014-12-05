<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * Money is a contract for something which has an internal economic value
 */
interface Money
{

    /**
     * Gets the value of this
     *
     * @return int
     */
    public function getAmount();

    /**
     * Gets the currency of this
     *
     * @return string iso currency
     */
    public function getCurrency();
}
