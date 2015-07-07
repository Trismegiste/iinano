<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

        $api = $this->getPaypalApi();

        $retour = $api->setExpressCheckout([
            'RETURNURL' => $this->generateUrl('return_from_payment', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'CANCELURL' => $this->generateUrl('return_from_payment', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'PAYMENTREQUEST_0_AMT' => 9.99,
            'PAYMENTREQUEST_0_PAYMENTACTION' => Api::PAYMENTACTION_SALE,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
            'NOSHIPPING' => Api::NOSHIPPING_NOT_DISPLAY_ADDRESS
        ]);
        print_r($retour);
        $url = $api->getAuthorizeTokenUrl($retour['TOKEN']);

        return $this->render('TrismegisteSocialBundle:Ticket:buy_new_ticket.html.twig', [
                    'payment_url' => $url
        ]);
    }

    public function returnFromPaymentAction(Request $request)
    {
        $detail = $this->getPaypalApi()->getExpressCheckoutDetails([
            'TOKEN' => $request->query->get('token')
        ]);

        print_r($detail);

        return new Response('coucou');
    }

    protected function getPaypalApi()
    {
        return new Api([
            'username' => 'trismegiste-facilitator_api1.voila.fr',
            'password' => 'UUEMF2XQL4EX3TYJ',
            'signature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31Ar98jnDSdjKrfA12tKK25f9kqu5Q',
            'sandbox' => true,
            'useraction' => Api::USERACTION_COMMIT
        ]);
    }

}
