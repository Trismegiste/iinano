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

    public function likeCommentaryAction($id, $uuid, $action, $wallNick, $wallFilter)
    {
        $repo = $this->get('social.commentary.repository');

        switch ($action) {
            case 'add':
                $repo->iLikeThat($id, $uuid);
                break;
            case 'remove':
                $repo->iUnlikeThat($id, $uuid);
                break;
            default:
                $this->createNotFoundException("Action $action");
        }

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], "anchor-$id-$uuid");
    }

    public function ajaxLikePublishAction($id, $action, $wallNick, $wallFilter)
    {
        $repo = $this->get('social.publishing.repository');

        switch ($action) {
            case 'add':
                $repo->iLikeThat($id);
                break;
            case 'remove':
                $repo->iUnlikeThat($id);
                break;
            default:
                $this->createNotFoundException("Action $action");
        }

        return $this->render('TrismegisteSocialBundle:Content:ajax/publishing_like_button.html.twig', [
                    'wallNick' => $wallNick,
                    'wallFilter' => $wallFilter,
                    'content' => $repo->findByPk($id)
        ]);
    }

    public function likePublishAction($id, $action, $wallNick, $wallFilter)
    {
        $repo = $this->get('social.publishing.repository');

        switch ($action) {
            case 'add':
                $repo->iLikeThat($id);
                break;
            case 'remove':
                $repo->iUnlikeThat($id);
                break;
            default:
                $this->createNotFoundException("Action $action");
        }

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
    }

}
