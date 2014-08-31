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

        $response = new Response();
        $response->setEtag(filemtime($file)); // @todo ETag is working and not LastModified : why ?

        if ($response->isNotModified($this->getRequest())) {
            return $response;
        }

        $response->headers->set('X-Sendfile', $file);
        $response->headers->set('Content-Type', 'image/jpeg');

        return $response;
    }

}