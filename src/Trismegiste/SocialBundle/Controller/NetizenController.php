<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;
use Symfony\Component\HttpFoundation\Request;
use Trismegiste\SocialBundle\Form\ProfileType;
use Trismegiste\SocialBundle\Security\TicketVoter;

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
                    'profile' => $user->getProfile(),
                    'lastTicket' => $user->getLastTicket()
        ]);
    }

    public function editAvatarAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $this->onlyAjaxRequest();

            $img = imagecreatefromstring(
                    base64_decode(
                            preg_replace(
                                    '#data:image/(jpg|jpeg);base64,#', '', $request->request->get('content'), 1)));

            $repo = $this->get('social.netizen.repository');
            $repo->updateAvatar($this->getUser(), $img);

            $this->pushFlash('notice', 'Avatar updated');

            return new \Symfony\Component\HttpFoundation\JsonResponse(['status' => 'ok']);
        }

        $author = $this->getUser()->getAuthor();

        return $this->render('TrismegisteSocialBundle:Netizen:avatar_edit.html.twig', ['author' => $author]);
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

    /**
     * The page to redirect after successful login
     */
    public function landingPageAction()
    {
        $secu = $this->get('security.context');

        // backoffice users :
        if ($secu->isGranted('ROLE_ADMIN')) {
            $route = 'admin_dashboard';
        } else if ($secu->isGranted('ROLE_MANAGER')) {
            $route = 'admin_dashboard';
        } else if ($secu->isGranted('ROLE_MODERATOR')) {
            $route = 'admin_abusive_pub_listing';
        }

        // front users :
        if (!isset($route)) {
            if ($secu->isGranted(TicketVoter::SUPPORTED_ATTRIBUTE)) {
                $route = 'content_index';
            } else {
                $route = 'confirm_buy_ticket';
            }
        }

        return $this->redirectRouteOk($route);
    }

}
