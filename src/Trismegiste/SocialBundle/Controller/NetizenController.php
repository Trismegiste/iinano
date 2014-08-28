<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * NetizenController is a controller for the user : profile, stats...
 */
class NetizenController extends Template
{

    public function showProfileAction()
    {
        return $this->render('TrismegisteSocialBundle:Netizen:profile_show.html.twig');
    }

}