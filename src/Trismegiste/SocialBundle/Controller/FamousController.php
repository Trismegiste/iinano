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

    public function addFanOnPublishAction(Request $request)
    {
        $pk = $request->get('id');
        $doc = $this->getRepository()->findByPk($pk);
        $this->addFanOn($doc);
        $this->getRepository()->persist($doc);

        return $this->redirectRouteOk('content_index', [], 'anchor-' . $pk);
    }

    public function removeFanOnPublishAction(Request $request)
    {
        $pk = $request->get('id');
        $doc = $this->getRepository()->findByPk($pk);
        $this->removeFanOn($doc);
        $this->getRepository()->persist($doc);

        return $this->redirectRouteOk('content_index', [], 'anchor-' . $pk);
    }

}
