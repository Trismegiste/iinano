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

        return $this->render('TrismegisteSocialBundle:Content:simplepost_form.html.twig', ['form' => $form->createView()]);
    }

    public function editAction($id)
    {
        $repo = $this->getRepository();
        $post = $repo->findByPk($id);

        $form = $this->createForm(new \Trismegiste\SocialBundle\Form\SimplePostForm()
                , $post
                , ['action' => $this->generateUrl('simplepost_edit', ['id' => $id])]
        );

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $newPost = $form->getData();
            $newPost->setLastEdited(new \DateTime());
            try {
                $repo->persist($newPost);
                // @todo flash
                return $this->redirectRouteOk('content_index');
            } catch (\MongoException $e) {
                
            }
        }

        return $this->render('TrismegisteSocialBundle:Content:simplepost_form.html.twig', ['form' => $form->createView()]);
    }

}