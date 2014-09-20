<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Trismegiste\SocialBundle\Form\ProfileType;

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
                    'follower' => $user->getFollowerIterator(),
                    'following' => $user->getFollowingIterator(),
                    'author' => $user->getAuthor(),
                    'profile' => $user->getProfile()
        ]);
    }

    public function getAvatarAction($filename)
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

    public function likeNetizenAction($id, $action, $wallNick, $wallFilter)
    {
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

        $this->pushFlash('notice', $message . $target->getUsername());

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter]);
    }

    public function followNetizenAction($id, $action, $wallNick, $wallFilter)
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

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter]);
    }

    public function editAvatarAction()
    {
        if ($request->getMethod() == 'POST') {
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
            $this->redirectRouteOk('netizen_show', ['author' => $author->getNickname()]);
        }

        return $this->render('TrismegisteSocialBundle:Netizen:profile_edit.html.twig', [
                    'form' => $form->createView(),
                    'author' => $author
        ]);
    }

}
