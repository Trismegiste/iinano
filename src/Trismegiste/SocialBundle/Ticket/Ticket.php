<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * Ticket is a entrance ticket. Acquired with a EntranceFee or a Coupon
 * Conceptually, in an e-commerce, this is an order
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

        $this->purchase = $purchaseSystem; // @todo embed or copy properties ?
        $this->purchasedAt = $now;
    }

    /**
     * @inheritdoc
     */
    public function isValid(\DateTime $now = null)
    {
        if (is_null($now)) {
            $now = new \DateTime();
        }

        $now->modify($this->purchase->getDuration());

        return $this->purchasedAt->getTimestamp() > $now->getTimestamp();
    }

    /**
     * @inheritdoc
     */
    public function getPurchasedAt()
    {
        return $this->purchasedAt;
    }

}
