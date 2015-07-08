<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
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
                    'payment_url' => $url,
                    'fee' => $this->get('social.ticket.repository')->findEntranceFee()
        ]);
    }

    public function returnFromPaymentAction(Request $request)
    {
        $paypal = $this->get('social.payment.paypal');

        try {
            $ret = $paypal->processReturnFromGateway($request);

            $this->pushFlash('notice', "Transaction number " . $ret);

            return $this->redirectRouteOk('payment_summary', ['id' => $ret]);
        } catch (\Exception $e) {
            $this->pushFlash('warning', $e->getMessage());

            return $this->redirectRouteOk('buy_new_ticket');
        }
    }

    public function cancelFromPaymentAction(Request $request)
    {
        $this->pushFlash('warning', "You have cancelled the payment");

        return $this->redirectRouteOk('buy_new_ticket');
    }

    /**
     * Redirection after return from paypal to here to prevent "refresh" from user
     */
    public function paymentSummaryAction($id)
    {
        return $this->render('TrismegisteSocialBundle:Ticket:payment_summary.html.twig', [
                    'transaction_id' => $id
        ]);
    }

}
