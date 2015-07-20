<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * RepeatController is a controller for Repeated published content
 */
class RepeatController extends ContentController
{

    public function ajaxRepeatAction($id, $wallNick, $wallFilter)
    {
        $this->onlyAjaxRequest();
        $repo = $this->get('social.publishing.repository');

        try {
            $pub = $repo->repeatPublishing($id);
            $response = new JsonResponse(['message' => "You've repeated a message from "
                . $pub->getEmbedded()->getAuthor()->getNickname()]);
        } catch (\RuntimeException $e) {
            $response = new JsonResponse(['message' => $e->getMessage()], 412);
        }

        return $response;
    }

}
