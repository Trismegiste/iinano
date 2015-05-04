<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Security\TicketVoter;

/**
 * TicketController is a controller for purchasing ticket w/ payment or coupon
 */
class TicketController extends Template
{

    public function confirmBuyTicketAction()
    {
        if ($this->get('security.context')->isGranted(TicketVoter::SUPPORTED_ATTRIBUTE)) {
            return $this->redirectRouteOk('content_index');
        }

        return $this->render('TrismegisteSocialBundle:Ticket:confirm_buy_ticket.html.twig');
    }

    public function returnFromPayment()
    {

    }

}
