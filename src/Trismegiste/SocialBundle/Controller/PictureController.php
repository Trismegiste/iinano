<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Gregwar\Image\Image;

/**
 * PictureController is a controller for local storage of picture
 */
class PictureController extends Template
{

    /**
     * Thumbnailing à la volée
     */
    public function getAction($storageKey, $size = 'full')
    {
        $file = $this->get('social.picture.storage')
                ->getAbsolutePath($storageKey);

//        $file = Image::open($file)
//                ->setCacheDir('/home/flo/Develop/iinano/storage/cache/')
//                ->resize(100)
//                ->guess();

        $response = new Response();
        $lastModif = new \DateTime();
        $lastModif->setTimestamp(filemtime($file));
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
