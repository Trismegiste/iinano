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

    public function sendAvatarAction($filename)
    {
        $file = $this->get('social.avatar.repository')
                ->getAvatarAbsolutePath($filename);

        $response = new Response('', 200, ['X-Sendfile' => $file, 'Content-Type' => 'image/jpeg']);

        return $response;
    }

}