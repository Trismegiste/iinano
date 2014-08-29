<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * NetizenController is a controller for the user : profile, stats...
 */
class NetizenController extends Template
{

    public function showProfileAction()
    {
        $user = $this->getUser();

        return $this->render('TrismegisteSocialBundle:Netizen:profile_show.html.twig', [
                    'author' => $user->getAuthor(),
                    'profile' => $user->getProfile()
        ]);
    }

    public function sendAvatarAction()
    {
        $basePath = $this->container->getParameter('kernel.root_dir') . '/static/';
        $file = $basePath . 'hinaitigo.png';

        $response = new Response();
        $response->headers->set('X-Sendfile', $file);
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }

}