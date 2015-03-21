<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * TicketController is a controller for purchasing ticket w/ payment or coupon
 */
class TicketController extends Template
{

    public function noValidTicketAction()
    {
        return $this->render('TrismegisteSocialBundle:Ticket:new_ticket.html.twig');
    }

}
