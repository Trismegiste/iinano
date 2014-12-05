<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * MoneyImpl is an implementation of Money
 */
trait MoneyImpl
{

    /** @var int */
    protected $amount = 0;

    /** ISO currency */
    protected $currency = 'USD';

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
