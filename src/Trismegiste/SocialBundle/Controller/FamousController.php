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

    protected function addFanOn(Famous $obj)
    {
        $obj->addFan($this->getAuthor());
    }

    protected function removeFanOn(Famous $obj)
    {
        $obj->removeFan($this->getAuthor());
    }

    public function addFanOnCommentAction(Request $request)
    {
        
    }

    public function addFanOnPublishAction($id)
    {
        $doc = $this->getRepository()->findByPk($id);
        $this->addFanOn($doc);
        $this->getRepository()->persist($doc);

        return $this->redirectRouteOk('content_index', [], 'anchor-' . $id);
    }

    public function removeFanOnPublishAction($id)
    {
        $doc = $this->getRepository()->findByPk($id);
        $this->removeFanOn($doc);
        $this->getRepository()->persist($doc);

        return $this->redirectRouteOk('content_index', [], 'anchor-' . $id);
    }

}
