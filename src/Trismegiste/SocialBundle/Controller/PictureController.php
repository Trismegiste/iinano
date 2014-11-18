<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * PictureController is a controller for local storage of picture
 */
class PictureController extends Template
{

    /**
     * Thumbnailing à la volée
     */
    public function getAction($storageKey, $size)
    {
        $file = $this->get('social.picture.storage')
                ->getImagePath($storageKey, $size);

        if (!file_exists($file)) {
            throw $this->createNotFoundException($storageKey . ' not found');
        }

        $response = new Response();
        $lastModif = \DateTime::createFromFormat('U', filemtime($file));
        $response->setLastModified($lastModif);
        $response->setEtag(filesize($file));
        $response->setPublic();

        if ($response->isNotModified($this->getRequest())) {
            return $response;
        }

        $response->headers->set('X-Sendfile', $file);
        $response->headers->set('Content-Type', 'image/' . pathinfo($storageKey, PATHINFO_EXTENSION));
        $this->get('logger')->debug("$storageKey xsended");

        return $response;
    }

}
