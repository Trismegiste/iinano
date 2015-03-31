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
        $session = $this->getRequest()->getSession();
        if ($session->has('coupon')) {
            $coupon = $this->get('social.ticket.repository')
                    ->findCouponByHash($session->get('coupon'));

            $this->get('social.ticket.repository')
                    ->persistNewTicketFromCoupon($user, $coupon);
        }



        return $this->render('TrismegisteSocialBundle:Ticket:new_ticket.html.twig');
    }

}
