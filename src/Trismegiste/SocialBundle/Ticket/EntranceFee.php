<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * EntranceFee is a payment for acquiring Ticket
 * Conceptually, in an e-commerce, this is a product template
 */
class EntranceFee extends PurchaseChoice
{

    /** @var numeric */
    protected $amount = 0;

    /** @var string ISO currency */
    protected $currency = 'XXX';

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setCurrency($curr)
    {
        $this->currency = $curr;
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

    /**
     * @inheritdoc
     */
    public function getDurationUnit()
    {
        return 'month';
    }

    public function getTitle()
    {
        return sprintf('payment %.2f %s', $this->amount, $this->currency);
    }

}
