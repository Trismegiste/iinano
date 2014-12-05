<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * Payment is a payment for acquiring Ticket
 */
class Payment extends AbstractPurchase
{

    protected $amount = 0;
    protected $currency = 'USD';

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

}
