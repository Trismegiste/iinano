<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DiscoverController implements the first of social networks :
 * "Never show an empty page"
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
                    'wallFilter' => 'all',
                    'pagination' => $this->container->getParameter('social.pagination')
        ]);
    }

    public function defaultContentIfEmptyAction()
    {
        return $this->render('TrismegisteSocialBundle:Discover:default_content_if_empty.html.twig', [
                    'last_registered' => $this->get('social.netizen.repository')
                            ->findLastRegistered(),
                    'last_content' => $this->get('social.publishing.repository')
                            ->findLastEntries(0, 10),
                    'wallUser' => $this->getUser(),
                    'wallNick' => $this->getUser()->getUsername(),
                    'wallFilter' => 'all'
        ]);
    }

    public function tourAction()
    {
        return $this->render('TrismegisteSocialBundle:Discover:app_tour.html.twig', [
                    'wallUser' => $this->getUser(),
                    'wallNick' => $this->getUser()->getUsername(),
                    'wallFilter' => 'self',
                    'pagination' => $this->container->getParameter('social.pagination'),
                    'last_content' => $this->get('social.publishing.repository')
                            ->findLastEntries(0, 10)
        ]);
    }

}
