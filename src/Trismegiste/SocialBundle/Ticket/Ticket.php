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

    public function __construct(PurchaseChoice $purchaseSystem, \DateTime $now = null)
    {
        if (is_null($now)) {
            $now = new \DateTime();
        }

        $this->purchase = $purchaseSystem;
        $this->purchasedAt = $now;
    }

    public function isValid(\DateTime $now = null)
    {
        if (is_null($now)) {
            $now = new \DateTime();
        }

        $now->sub($this->purchase->getDuration());

        return $this->purchasedAt->getTimestamp() > $now->getTimestamp();
    }

    public function getPurchasedAt()
    {
        return $this->purchasedAt;
    }

}
