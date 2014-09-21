<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * SocialController is a controller for social actions between user
 */
class SocialController extends Template
{

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

}
