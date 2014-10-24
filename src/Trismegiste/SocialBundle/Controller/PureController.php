<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

/**
 * PureController is a ...
 */
class PureController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{

    public function indexAction()
    {
        return $this->render('TrismegisteSocialBundle::base.html.twig');
    }

}
