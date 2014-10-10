<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Trismegiste\SocialBundle\Form\PictureAutoUploaderType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * PictureController is a controller for local storage of picture
 */
class PictureController extends Template
{

    /**
     * Thumbnailing à la volée
     */
    public function getAction($storageKey)
    {
        $file = $this->get('social.avatar.repository')
                ->getAvatarAbsolutePath($storageKey);

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

    /**
     * Create and upload action for a picture
     */
    public function uploadAction(Request $request)
    {
        $this->onlyAjaxRequest();

        $form = $this->createForm(new PictureAutoUploaderType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            /* @var $picture \Symfony\Component\HttpFoundation\File\UploadedFile */
            $picture = $form->getData()['picture'];
            $targetDir = $this->container->getParameter('kernel.root_dir') . '/../storage/';
            $extension = [];
            preg_match('#^image/(jpg|jpeg|gif|png)$#', $picture->getMimeType(), $extension);
            $name = bin2hex($this->getAuthor()->getNickname()) . '-' . time() . '.' . $extension[1];

            $repo = $this->get('social.publishing.repository');
            $doc = $repo->create('picture');
            $doc->setMimeType($picture->getMimeType());
            $doc->setStorageKey($name);
            $picture->move($targetDir, $name);
            $repo->persist($doc);
sleep(5);
            return new JsonResponse([
                'redirect' => $this->generateUrl('publishing_edit', ['id' => (string) $doc->getId()])
                    ], 201
            );
        }

        return new JsonResponse($form->getErrors(), 500);
    }

    public function renderFormAction()
    {
        $form = $this->createForm(new PictureAutoUploaderType());

        return $this->render('TrismegisteSocialBundle:Picture:autoupload.html.twig'
                        , ['form' => $form->createView()]);
    }

}
