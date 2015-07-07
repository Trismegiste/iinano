<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Security\TicketVoter;

/**
 * TicketController is a controller for purchasing ticket w/ payment or coupon
 */
class TicketController extends Template
{

    public function buyNewTicketAction()
    {
        if ($this->get('security.context')->isGranted(TicketVoter::SUPPORTED_ATTRIBUTE)) {
            return $this->redirectRouteOk('content_index');
        }

        $paypal = $this->get('social.payment.paypal');
        $url = $paypal->getUrlToGateway();

        return $this->render('TrismegisteSocialBundle:Ticket:buy_new_ticket.html.twig', [
                    'payment_url' => $url
        ]);
    }

    public function returnFromPaymentAction(Request $request)
    {
        $paypal = $this->get('social.payment.paypal');
        $ret = $paypal->processReturnFromGateway($request);

        print_r($ret);

        return new Response('coucou');
    }

}
