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

    public function confirmBuyTicketAction()
    {
        return $this->render('TrismegisteSocialBundle:Ticket:confirm_buy_ticket.html.twig');
    }

}
