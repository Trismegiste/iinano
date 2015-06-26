<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\TicketType;
use Trismegiste\SocialBundle\Tests\Unit\Form\FormTestCase;
use Trismegiste\SocialBundle\Ticket\Coupon;
use Trismegiste\SocialBundle\Ticket\Ticket;

/**
 * TicketTypeTest tests form type for netizen's ticket
 */
class TicketTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new TicketType();
    }

    protected function createData()
    {
        $coupon = new Coupon();
        $coupon->expiredAt = new \DateTime('2015-01-01');
        $ticket = new Ticket($coupon, new \DateTime('2015-01-02'));

        return $ticket;
    }

    public function getInvalidInputs()
    {
        $ticket = $this->createData();

        return [
            [['expiredAt' => 123], $ticket, ['expiredAt']]
        ];
    }

    public function getValidInputs()
    {
        $ticket = $this->createData();
        $ticket->setExpiredAt(new \DateTime('2016-05-22'));

        return [
            [['expiredAt' => ['year' => 2016, "month" => 5, 'day' => 22]], $ticket]
        ];
    }

}
