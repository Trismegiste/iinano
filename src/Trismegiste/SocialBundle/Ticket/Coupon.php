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

    /** @var string */
    protected $hashKey;

    /**
     * Ctor
     *
     * @param \DateInterval $duration the given duration for this purchase
     * @param string $code an asbtract key for this coupon. Must be unpredictible (unlike MongoId)
     * @param \DateTime $expiration expiration date (default: in 5 days)
     */
    public function __construct(\DateInterval $duration, $code, \DateTime $expiration = null)
    {
        $this->duration = $duration;
        $this->hashKey = $code;

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
        return $this->hashKey; // @todo hashids here could be cool
    }

}
