<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\Socialist\Famous;
use Symfony\Component\HttpFoundation\Request;

/**
 * FamousController is a controller to manage action on entities with Famous contract
 */
class FamousController extends Template
{

    public function addFanOnCommentaryAction($id, $uuid)
    {
        $pub = $this->getRepository()->findByPk($id);
        $commentary = $pub->getCommentaryByUuid($uuid);
        $commentary->addFan($this->getAuthor());
        $this->getRepository()->persist($pub);

        return $this->redirectRouteOk('content_index', [], 'anchor-' . $id);
    }

    public function removeFanOnCommentaryAction($id, $uuid)
    {
        $pub = $this->getRepository()->findByPk($id);
        $commentary = $pub->getCommentaryByUuid($uuid);
        $commentary->removeFan($this->getAuthor());
        $this->getRepository()->persist($pub);

        return $this->redirectRouteOk('content_index', [], 'anchor-' . $id);
    }

    public function addFanOnPublishAction($id)
    {
        $doc = $this->getRepository()->findByPk($id);
        $doc->addFan($this->getAuthor());
        $this->getRepository()->persist($doc);

        return $this->redirectRouteOk('content_index', [], 'anchor-' . $id);
    }

    public function removeFanOnPublishAction($id)
    {
        $doc = $this->getRepository()->findByPk($id);
        $doc->removeFan($this->getAuthor());
        $this->getRepository()->persist($doc);

        return $this->redirectRouteOk('content_index', [], 'anchor-' . $id);
    }

}
