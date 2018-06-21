<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * Coupon is a coupon for acquiring Ticket
 */
class Coupon extends PurchaseChoice
{

    /** @var \DateTime how long this coupon can be used */
    public $expiredAt;

    /** @var string */
    public $hashKey;

    /** @var integer how many times this coupon is used */
    protected $usedCounter = 0;

    /** @var integer how many times this coupon can be used */
    public $maximumUse;

    public function __construct()
    {
        $this->expiredAt = new \DateTime('+3 month');
        $this->maximumUse = 100;
        $this->setDurationValue(365);
    }

    /**
     * Gets the hash key code for this coupon
     *
     * @return string
     */
    public function getHashKey()
    {
        return $this->hashKey;
    }

    /**
     * Incrementing the use of this coupon
     */
    public function incUse()
    {
        $this->usedCounter++;
    }

    /**
     * Is this coupon still valid ?
     *
     * @return bool
     */
    public function isValid()
    {
        return
                (($this->usedCounter < $this->maximumUse) &&
                ( time() < $this->expiredAt->getTimestamp() ));
    }

    /**
     * returns how many times this coupon was used
     *
     * @return integer
     */
    public function getUsedCounter()
    {
        return $this->usedCounter;
    }

    /**
     * @inheritdoc
     */
    public function getDurationUnit()
    {
        return 'day';
    }

    public function getTitle()
    {
        return 'coupon ' . $this->hashKey;
    }

}
