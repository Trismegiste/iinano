<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

use Trismegiste\Yuurei\Persistence;

/**
 * PurchaseChoice is a contract for different systems for acquiring Ticket
 */
abstract class PurchaseChoice implements Persistence\Persistable
{

    use Persistence\PersistableImpl;

    /** @var integer a time duration value in time unit */
    protected $duration;

    /**
     * Gets the duration offset of this PurchaseChoice
     *
     * @return string a string used by DateTime::modify()
     */
    public function getDurationOffset()
    {
        return '+' . $this->duration . ' ' . $this->getDurationUnit();
    }

    /**
     * Get the time unit for the duration (day|month|...)
     *
     * @return string
     */
    abstract public function getDurationUnit();

    /**
     * Get the duration in the time unit (see below)
     *
     * @return integer
     */
    public function getDurationValue()
    {
        return $this->duration;
    }

    /**
     * Set the duration in the time unit
     *
     * @param integer $num
     */
    public function setDurationValue($num)
    {
        $this->duration = (int) $num;
    }

}
