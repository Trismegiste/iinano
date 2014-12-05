<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * Coupon is a coupon for acquiring Ticket
 */
class Coupon implements PurchaseChoice
{

    /** @var \DateInterval */
    protected $duration;

    /**
     * Ctor
     *
     * @param \DateInterval $duration the given duration for this purchase
     */
    public function __construct(\DateInterval $duration)
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

}
