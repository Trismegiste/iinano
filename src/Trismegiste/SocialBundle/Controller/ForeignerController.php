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

/**
 * ForeignerController is a controller for unathentificated user
 */
class ForeignerController extends Template
{

    public function aboutAction()
    {
        return $this->render('TrismegisteSocialBundle:Foreigner:about.html.twig');
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

        return $this->render('TrismegisteSocialBundle:Foreigner:login.html.twig', ['error' => $error]);
    }

    public function registerAction(Request $request)
    {
        // @todo block all users full authenticated
        $repo = $this->get('social.netizen.repository');
        $form = $this->createForm('netizen_register');

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // retrieve data
                $user = $form->getData();
                $avatar = $form->get('avatar')->getData();
                // persist
                $repo->persist($user);
                $repo->persist($user, $avatar);
                $this->authenticateAccount($user);

                return $this->redirectRouteOk('content_index');
            } else {
                
            }
        }

        return $this->render('TrismegisteSocialBundle:Foreigner:register.html.twig', ['register' => $form->createView()]);
    }

    /**
     * Automatic post-registration user authentication
     */
    protected function authenticateAccount(Netizen $account)
    {
        $token = new UsernamePasswordToken($account, null, 'secured_area', $account->getRoles());
        $this->get('security.context')->setToken($token);
    }

}