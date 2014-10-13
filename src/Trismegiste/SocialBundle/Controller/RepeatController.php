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

    public function repeatAction($id)
    {
        $repo = $this->get('social.publishing.repository');

        /* @var $pub \Trismegiste\Socialist\Repeat */
        $pub = $repo->create('repeat');
        $original = $repo->findByPk($id);
        $pub->setEmbedded($original);
        $repo->persist($pub);

        return $this->redirectRouteOk('wall_index', [
                    'wallNick' => $this->getUser()->getUsername(),
                    'wallFilter' => 'self'
                        ], 'anchor-' . $pub->getId());
    }

}
