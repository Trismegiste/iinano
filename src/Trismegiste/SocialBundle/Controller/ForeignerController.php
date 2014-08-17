<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;

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
        return $this->render('TrismegisteSocialBundle:Foreigner:login.html.twig');
    }

}