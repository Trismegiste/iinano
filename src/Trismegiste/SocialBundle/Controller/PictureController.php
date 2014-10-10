<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Trismegiste\SocialBundle\Form\Picture\LocalStorageType;

/**
 * PictureController is a controller for local storage of picture
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

    /**
     * The "two-stage upload" : first part, binary
     * Why this ?
     * For two reasons :
     * 1. direct upload using Amazon S3 POST system need 2 post requests
     * 2. managing the uploadedFile in another action keeps the PublishingController unaware of
     *    specific treatment for the Picture entity persistence.
     *
     * So even for local storage, I use the "two-stage upload".
     * The second part, the metadatas are handled by the publishing controller
     */
    public function uploadAction(Request $request)
    {
        $this->onlyAjaxRequest();

        $form = $this->createForm(new LocalStorageType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            /* @var $picture \Symfony\Component\HttpFoundation\File\UploadedFile */
            $picture = $form->getData()['picture'];
            $targetDir = $this->container->getParameter('kernel.root_dir') . '/../storage/';
            $name = bin2hex($this->getAuthor()->getNickname()) . '-' . time();
            $picture->move($targetDir, $name);

            return new Response('', 201);
        }

        return new Response(json_encode($form->getErrors()), 500);
    }

    public function renderFormAction()
    {
        $form = $this->createForm(new LocalStorageType());

        return $this->render('TrismegisteSocialBundle:Picture:localstorage.html.twig'
                        , ['form' => $form->createView()]);
    }

}
