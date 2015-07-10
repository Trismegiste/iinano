<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * LandingPageSuccessHandler is a AuthenticationSuccessHandler for the security layer
 */
class LandingPageSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    protected $httpUtils;

    /** @var SecurityContextInterface */
    protected $security;

    public function __construct(HttpUtils $httpUtils, SecurityContextInterface $secu)
    {
        $this->httpUtils = $httpUtils;
        $this->security = $secu;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // set a cookie to store the oauth provider for next connection
        $cookie = new Cookie('oauth_provider', $token->getUser()
                        ->getCredential()->getProviderKey(), new \DateTime('+1 month'));

        $route = 'buy_new_ticket'; // @todo with the new 403 listener, this route is useless

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $route = 'admin_dashboard';
        } else if ($this->security->isGranted('ROLE_MANAGER')) {
            $route = 'admin_netizen_listing';
        } else if ($this->security->isGranted('ROLE_MODERATOR')) {
            $route = 'admin_abusive_pub_listing';
        } else if ($this->security->isGranted(TicketVoter::SUPPORTED_ATTRIBUTE)) {
            $route = 'content_index';
        }

        $response = $this->httpUtils->createRedirectResponse($request, $route);
        $response->headers->setCookie($cookie);

        return $response;
    }

}
