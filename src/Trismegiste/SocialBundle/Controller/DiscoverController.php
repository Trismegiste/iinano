<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DiscoverController is a ...
 */
class DiscoverController extends Controller
{

    public function showAction()
    {

        return $this->render('TrismegisteSocialBundle:Discover:show.html.twig', [
        ]);
    }

}
