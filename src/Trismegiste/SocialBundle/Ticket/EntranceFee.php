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

    /** @var string ISO currency */
    protected $currency = 'XXX';

    /** @var \DateInterval */
    protected $duration;

    public function setDuration($str)
    {
        $this->duration = new \DateInterval($str);
    }

    public function setAmount($amount)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Invalid negative or null amout");
        }
        $this->amount = $amount;
    }

    public function setCurrency($curr)
    {
        if (3 !== strlen($curr)) {
            throw new \InvalidArgumentException("Invalid currency");
        }
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
