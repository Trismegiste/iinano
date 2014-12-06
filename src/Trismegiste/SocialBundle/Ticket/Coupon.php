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

    /** @var \DateInterval write-only */
    protected $duration;

    /** @var \DateTime read/write */
    protected $expiredAt;

    /**
     * Ctor
     *
     * @param \DateInterval $duration the given duration for this purchase
     * @param \DateTime $expiration expiration date (default: in 5 days)
     */
    public function __construct(\DateInterval $duration, \DateTime $expiration = null)
    {
        $this->duration = $duration;

        if (is_null($expiration)) {
            $expiration = new \DateTime();
            $expiration->modify("+5 days");
        }
        $this->expiredAt = $expiration;
    }

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
        return (string) $this->getId(); // @todo hashids here could be cool
    }

}
