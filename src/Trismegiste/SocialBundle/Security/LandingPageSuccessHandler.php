<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * LandingPageSuccessHandler is a ...
 */
class LandingPageSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    protected $httpUtils;

    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        switch ($token->getUser()->getGroup()) {
            case 'ROLE_ADMIN': $route = 'admin_dashboard';
                break;
            case 'ROLE_MANAGER': $route = 'admin_netizen_listing';
                break;
            case 'ROLE_MODERATOR': $route = 'admin_abusive_pub_listing';
                break;
            case 'ROLE_USER': $route = 'content_index';
                break;
            default: $route = 'buy_new_ticket';
        }
//        if ($secu->isGranted()) {
//
//        } else if ($secu->isGranted('')) {
//            $route = '';
//        } else if ($secu->isGranted('')) {
//            $route = '';
//        } else if ($secu->isGranted(TicketVoter::SUPPORTED_ATTRIBUTE)) {
//            $route = '';
//        }

        return $this->httpUtils->createRedirectResponse($request, $route);
    }

}
