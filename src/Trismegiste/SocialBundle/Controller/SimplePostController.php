<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\Socialist\SimplePost;
use Trismegiste\Socialist\Commentary;
use Symfony\Component\HttpFoundation\Request;

/**
 * SimplePostController is the main controller for CRUD of SimplePost
 */
class SimplePostController extends ContentController
{

    public function createAction()
    {
        $repo = $this->getRepository();

        $form = $this->createForm(new \Trismegiste\SocialBundle\Form\SimplePostForm()
                , new SimplePost($this->getUser()->getAuthor())
                , ['action' => $this->generateUrl('simplepost_create')]
        );

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $newPost = $form->getData();
            try {
                $repo->persist($newPost);
                // @todo flash
                return $this->redirectRouteOk('content_index');
            } catch (\MongoException $e) {
                
            }
        }

        return $this->render('TrismegisteSocialBundle:Content:simplepost_create.html.twig', ['form' => $form->createView()]);
    }

}