<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * EntranceFee is a payment for acquiring Ticket
 */
class EntranceFee implements PurchaseChoice
{

    /** @var int */
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
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->currency;
    }

}
