<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Trismegiste\SocialBundle\Security\Netizen;
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

    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('TrismegisteSocialBundle:Guest:login.html.twig', ['error' => $error]);
    }

    public function registerAction(Request $request)
    {
        // @todo block all users full authenticated

        $repo = $this->get('social.netizen.repository');
        $form = $this->createForm('netizen_register');
        // is there a coupon in session ?
        $session = $this->getRequest()->getSession();
        if ($session->has('coupon')) {
            $form->get('optionalCoupon')->setData($session->get('coupon'));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // only user data data
            $user = $form->getData();
            $repo->persist($user);
            $this->authenticateAccount($user);
            // coupon
            $coupon = $form->get('optionalCoupon')->getData();
            if (!empty($coupon)) {
                try {
                    $this->get('social.ticket.repository')->useCouponFor($user, $coupon);
                } catch (Ticket\InvalidCouponException $e) {
                    $this->pushFlash('warning', 'This coupon has expired or has been used too many times');
                }
            }

            return $this->redirectRouteOk('no_valid_ticket');
        }

        $param['register'] = $form->createView();

        return $this->render('TrismegisteSocialBundle:Guest:register.html.twig', $param);
    }

    /**
     * Automatic post-registration user authentication
     */
    protected function authenticateAccount(Netizen $account)
    {
        $token = new UsernamePasswordToken($account, null, 'secured_area', $account->getRoles());
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

        return $this->redirectRouteOk('guest_register');
    }

}
