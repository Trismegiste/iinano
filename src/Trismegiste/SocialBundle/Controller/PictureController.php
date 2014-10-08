<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

/**
 * PictureController is an example with MognoDb storage for picture
 */
class PictureController extends Template
{

    public function getAction($id)
    {
        $doc = $this->getRepository()->findByPk($id);

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setLastModified($doc->getLastEdited());
        $response->setPublic();
        $response->headers->set('Content-type', 'image/jpeg');

        if (!$response->isNotModified($this->getRequest())) {
            $response->setContent($doc->getStorageKey()->bin);
        }

        return $response;
    }

}
