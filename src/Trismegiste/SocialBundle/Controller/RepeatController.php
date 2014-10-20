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
        $original = $repo->findByPk($id);

        /* @var $pub \Trismegiste\Socialist\Repeat */
        $pub = $repo->create('repeat');
        $pub->setEmbedded($original);
        $repo->persist($pub);

        return $this->redirectRouteOk('wall_index', [
                    'wallNick' => $wallNick,
                    'wallFilter' => $wallFilter
                        ], 'anchor-' . $original->getId());
    }

}
