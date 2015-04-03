<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

use Trismegiste\Yuurei\Persistence;

/**
 * Coupon is a coupon for acquiring Ticket
 */
class Coupon implements PurchaseChoice, Persistence\Persistable
{

    use Persistence\PersistableImpl;

    /** @var string a string for DateTime::modify */
    public $duration;

    /** @var \DateTime how long this coupon can be used */
    public $expiredAt;

    /** @var string */
    public $hashKey;

    /** @var integer how many times this coupon is used */
    protected $usedCounter = 0;

    /** @var integer how many times this coupon can be used */
    public $maximumUse = 1;

    /**
     * @inheritdoc
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Gets the hash key code for this coupon
     *
     * @return string
     */
    public function getHashKey()
    {
        return $this->hashKey; // @todo hashids here could be cool
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

}
