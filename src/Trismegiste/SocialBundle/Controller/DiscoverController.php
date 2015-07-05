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
                    'last_registered' => $this->get('social.netizen.repository')
                            ->findLastRegistered(),
                    'last_content' => $this->get('social.publishing.repository')
                            ->findLastEntries(0, 10),
                    'wallUser' => $this->getUser(),
                    'wallNick' => $this->getUser()->getUsername(),
                    'wallFilter' => 'all'
        ]);
    }

}
