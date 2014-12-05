<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * Ticket is a entrance ticket. Acquired with a Payment or a Coupon
 */
class Ticket implements EntranceAccess
{

    /** @var PurchaseChoice */
    protected $purchase;

    /** @var \DateTime */
    protected $purchasedAt;

    public function __construct(PurchaseChoice $purchaseSystem, \DateTime $purchasedAt)
    {
        $this->purchase = $purchaseSystem;
    }

    public function isValid(\DateTime $now = null)
    {
        if (is_null($now)) {
            $now = new \DateTime();
        }

        $endPeriod = $this->purchasedAt->add($this->purchase->getDuration());

        return $endPeriod < $now;
    }

    public function getPurchasedAt()
    {
        return $this->purchasedAt;
    }

}
