<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * TicketController is a landing page for payment/coupon to obtain ticket
 */
class TicketController extends Template
{

    public function acquireTicketAction()
    {
        return new \Symfony\Component\HttpFoundation\Response('check ticket ?');
    }

}
