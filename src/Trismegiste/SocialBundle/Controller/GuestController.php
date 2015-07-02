<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Trismegiste\OAuthBundle\Security\Token;
use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\SocialBundle\Security\NotRegisteredHandler;
use Trismegiste\SocialBundle\Ticket;

/**
 * GuestController is a controller for unathentificated user
 */
class GuestController extends Template
{

    public function aboutAction()
    {
        return $this->render('TrismegisteSocialBundle:Guest:about.html.twig');
    }

    public function registerAction(Request $request)
    {
        $session = $this->getRequest()->getSession();

        if (!$session->has(NotRegisteredHandler::IDENTIFIED_TOKEN)) {
            throw new AccessDeniedHttpException("Not identified");
        }
        $this->assertNotAuthenticated();

        $repo = $this->get('social.netizen.repository');
        $form = $this->createForm('netizen_register', null, [
            'minimumAge' => $this->get('social.dynamic_config')['minimumAge']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // only user data data
            $user = $form->getData();
            $repo->persist($user);
            $this->authenticateAccount($user);
            $session->remove(NotRegisteredHandler::IDENTIFIED_TOKEN);
            // coupon
            $coupon = $session->get('coupon');
            if (!empty($coupon)) {
                try {
                    $this->get('social.ticket.repository')->useCouponFor($coupon);
                    $session->remove('coupon');
                } catch (Ticket\InvalidCouponException $e) {
                    $this->pushFlash('warning', $e->getMessage());
                }
            }
            // @todo use the success login handler
            return $this->redirectRouteOk('netizen_landing_page');
        }

        return $this->render('TrismegisteSocialBundle:Guest:register.html.twig', ['register' => $form->createView()]);
    }

    /**
     * Automatic post-registration user authentication
     */
    protected function authenticateAccount(Netizen $account)
    {
        $cred = $account->getCredential();
        $token = new Token('secured_area', $cred->getProviderKey(), $cred->getUid(), $account->getRoles());
        $token->setUser($account);
        $this->get('security.context')->setToken($token);
    }

    /**
     * Landing page when a guest gets here with a coupon
     */
    public function couponLandingAction($code)
    {
        $session = $this->getRequest()->getSession();
        // we add the coupon in session n matter how it is valid or not
        $session->set('coupon', $code);

        return $this->redirectRouteOk('trismegiste_oauth_connect');
    }

    public function connectAction()
    {
        $this->assertNotAuthenticated();
        $request = $this->getRequest();
        $session = $request->getSession();
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $config = $this->get('oauth.provider.factory')->getAvaliableProvider();

        return $this->render('TrismegisteSocialBundle:Guest:connect.html.twig', [
                    'listing' => $config, 'error' => $error]);
    }

    protected function assertNotAuthenticated()
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedHttpException("Already authenticated");
        }
    }

}
