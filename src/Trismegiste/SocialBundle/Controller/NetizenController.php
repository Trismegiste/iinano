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

    public function showProfileAction($author)
    {
        if ($author == $this->getUser()->getUsername()) {
            $user = $this->getUser();
        } else {
            $user = $this->get('social.netizen.repository')->findByNickname($author);
        }

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
        $response->setPublic();

        if ($response->isNotModified($this->getRequest())) {
            return $response;
        }

        $response->headers->set('X-Sendfile', $file);
        $response->headers->set('Content-Type', 'image/jpeg');

        return $response;
    }

    public function likeNetizenAction($id, $action)
    {
        // @todo refactoriser les like sur simple_post et commentaire sur le même principe de l'action en param
        $repo = $this->get('social.netizen.repository');
        $target = $repo->findByPk($id);
        $me = $this->getUser();

        switch ($action) {
            case 'add':
                $target->addFan($me->getAuthor());
                $message = "You like ";
                break;
            case 'remove':
                $target->removeFan($me->getAuthor());
                $message = "You unlike ";
                break;
        }

        $repo->persist($target);

        $this->pushFlash('notice', $message . $target->getAuthor()->getNickname());

        return $this->redirectRouteOk('content_index');
    }

    public function followNetizenAction($id, $action)
    {
        $repo = $this->get('social.netizen.repository');
        $following = $repo->findByPk($id);
        $me = $this->getUser();

        switch ($action) {
            case 'add':
                $me->follow($following);
                $message = "You're following ";
                break;
            case 'remove':
                $me->unfollow($following);
                $message = "You no longer follow ";
                break;
        }

        $repo->persist($me);
        $repo->persist($following);

        $this->pushFlash('notice', $message . $following->getAuthor()->getNickname());

        return $this->redirectRouteOk('content_index');
    }

}