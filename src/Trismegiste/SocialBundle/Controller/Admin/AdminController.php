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
        $param = [
            'user' => $this->get('social.netizen.repository')->countAllUser(),
            'content' => $this->get('social.publishing.repository')->countAllPublishing()
        ];

        return $this->render('TrismegisteSocialBundle:Admin:dashboard.html.twig', $param);
    }

}
