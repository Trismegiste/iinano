<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

/**
 * RepeatController is a controller for Repeated published content
 */
class RepeatController extends ContentController
{

    public function repeatAction($id, $wallNick, $wallFilter)
    {
        $repo = $this->get('social.publishing.repository');

        try {
            $repo->repeatPublishing($id);
        } catch (\RuntimeException $e) {
            $this->pushFlash('warning', $e->getMessage());
        }

        return $this->redirectRouteOk('wall_index', [
                    'wallNick' => $wallNick,
                    'wallFilter' => $wallFilter
                        ], 'anchor-' . $id);
    }

}
