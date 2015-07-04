<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

/**
 * HelpController shows help
 */
class HelpController extends Template
{

    public function showAction($id)
    {
        return $this->render('TrismegisteSocialBundle:help:' . $id . '.html.twig');
    }

}
