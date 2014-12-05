<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * AbstractPurchase is an abstract implementation of PurchaseChoice
 */
abstract class AbstractPurchase implements PurchaseChoice
{

    /** @var \DateInterval */
    protected $duration;

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
