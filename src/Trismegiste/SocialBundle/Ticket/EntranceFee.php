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
    protected $currency;

    /** @var \DateInterval */
    protected $duration;

    /**
     * Ctor
     *
     * @param \DateInterval $duration the given duration for this purchase
     */
    public function __construct(\DateInterval $duration, $amount, $curr)
    {
        if (empty($curr)) {
            throw new \InvalidArgumentException("Invalid currency");
        }
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Invalid negative or null amout");
        }
        $this->duration = $duration;
        $this->amount = $amount;
        $this->currency = $curr;
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
