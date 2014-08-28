<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

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
        $form = $this->createForm(new \Trismegiste\SocialBundle\Form\RegisterType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            
        }

        return $this->render('TrismegisteSocialBundle:Foreigner:register.html.twig', ['register' => $form->createView()]);
    }

}