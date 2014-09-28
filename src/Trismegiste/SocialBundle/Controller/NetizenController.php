<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Trismegiste\SocialBundle\Form\ProfileType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Trismegiste\SocialBundle\Utils\KeyIterator;

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

        $follower = $this->get('social.netizen.repository')
                ->findBatchNickname($user->getFollowerIterator());
        $following = $this->get('social.netizen.repository')
                ->findBatchNickname($user->getFollowingIterator());

        return $this->render('TrismegisteSocialBundle:Netizen:profile_show.html.twig', [
                    'follower' => $follower,
                    'following' => $following,
                    'author' => $user->getAuthor(),
                    'profile' => $user->getProfile()
        ]);
    }

    public function getAvatarAction($filename)
    {
        $file = $this->get('social.avatar.repository')
                ->getAvatarAbsolutePath($filename);

        $response = new Response();
        $lastModif = new \DateTime();
        $lastModif->setTimestamp(filemtime($file));
        $response->setLastModified($lastModif);
        $response->setEtag(filesize($file));
        $response->setPublic();

        if ($response->isNotModified($this->getRequest())) {
            return $response;
        }

        $response->headers->set('X-Sendfile', $file);
        $response->headers->set('Content-Type', 'image/jpeg');
        $this->get('logger')->debug("$filename xsended");

        return $response;
    }

    public function editAvatarAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            if (!$request->isXmlHttpRequest()) {
                throw new AccessDeniedException('U haxxor');
            }

            $img = imagecreatefromstring(
                    base64_decode(
                            preg_replace(
                                    '#data:image/(jpg|jpeg);base64,#', '', $request->request->get('content'), 1)));

            $repo = $this->get('social.netizen.repository');
            $repo->updateAvatar($this->getUser(), $img);
        }

        return $this->render('TrismegisteSocialBundle:Netizen:avatar_edit.html.twig');
    }

    public function editProfileAction(Request $request)
    {
        $author = $this->getUser()->getAuthor();
        $profile = $this->getUser()->getProfile();
        $form = $this->createForm(new ProfileType(), $profile);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $repo = $this->get('social.netizen.repository');
            $user = $repo->findByPk($this->getUser()->getId());
            $user->setProfile($form->getData());
            $repo->persist($user);
            $this->pushFlash('notice', 'Profile updated');

            return $this->redirectRouteOk('netizen_show', ['author' => $author->getNickname()]);
        }

        return $this->render('TrismegisteSocialBundle:Netizen:profile_edit.html.twig', [
                    'form' => $form->createView(),
                    'author' => $author
        ]);
    }

}
