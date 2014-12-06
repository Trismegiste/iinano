<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

use Trismegiste\Yuurei\Persistence;

/**
 * EntranceFee is a payment for acquiring Ticket
 * Conceptually, in an e-commerce, this is a product template
 */
class EntranceFee implements PurchaseChoice, Persistence\Persistable
{

    use Persistence\PersistableImpl;

    /** @var numeric */
    protected $amount = 0;

    /** ISO currency */
    protected $currency = 'USD';

    /** @var \DateInterval */
    protected $duration;

    /**
     * Ctor
     *
     * @param \DateInterval $duration the given duration for this purchase
     */
    public function __construct(\DateInterval $duration, $amount, $curr)
    {
        $this->duration = $duration;
    }

    /**
     * @inheritdoc
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Returns the subscription fee
     *
     * @return numeric
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Returns the iso currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

}
