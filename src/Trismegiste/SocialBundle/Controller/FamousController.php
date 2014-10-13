<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * FamousController is a controller to manage action on entities with Famous contract
 */
class FamousController extends Template
{

    public function ajaxLikeCommentaryAction($id, $uuid, $action, $wallNick, $wallFilter)
    {
        $this->onlyAjaxRequest();
        $repo = $this->get('social.commentary.repository');

        switch ($action) {
            case 'add':
                $pub = $repo->iLikeThat($id, $uuid);
                break;
            case 'remove':
                $pub = $repo->iUnlikeThat($id, $uuid);
                break;
            default:
                $this->createNotFoundException("Action $action");
        }

        return $this->render('TrismegisteSocialBundle:Content:ajax/commentary_like_button.html.twig', [
                    'wallNick' => $wallNick, // to keep the more stateless as possible
                    'wallFilter' => $wallFilter,
                    'content' => $pub,
                    'comment' => $pub->getCommentaryByUuid($uuid)
        ]);
    }

    public function ajaxLikePublishAction($id, $action, $wallNick, $wallFilter)
    {
        $this->onlyAjaxRequest();
        $repo = $this->get('social.publishing.repository');

        switch ($action) {
            case 'add':
                $pub = $repo->iLikeThat($id);
                break;
            case 'remove':
                $pub = $repo->iUnlikeThat($id);
                break;
            default:
                $this->createNotFoundException("Action $action");
        }

        return $this->render('TrismegisteSocialBundle:Content:ajax/publishing_like_button.html.twig', [
                    'wallNick' => $wallNick, // to keep the more stateless as possible
                    'wallFilter' => $wallFilter,
                    'content' => $pub
        ]);
    }

}
