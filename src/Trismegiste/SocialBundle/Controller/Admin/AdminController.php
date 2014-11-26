<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * AdminController is a controller for a global admin
 */
class AdminController extends Template
{

    public function dashboardAction()
    {
        return $this->render('TrismegisteSocialBundle:Admin:dashboard.html.twig');
    }

}
