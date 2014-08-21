<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\Socialist\SimplePost;
use Symfony\Component\Form\Form;
use Trismegiste\SocialBundle\Form\SimplePostForm;

/**
 * SimplePostController is the main controller for CRUD of SimplePost
 */
class SimplePostController extends ContentController
{

    protected function processForm(Form $form)
    {
        $repo = $this->getRepository();

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $newPost = $form->getData();
            $newPost->setLastEdited(new \DateTime());
            try {
                $repo->persist($newPost);
                $this->pushFlash('notice', 'Message saved');
                return $this->redirectRouteOk('content_index', [], 'anchor-' . $newPost->getId());
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save message');
            }
        }

        return $this->render('TrismegisteSocialBundle:Content:simplepost_form.html.twig', ['form' => $form->createView()]);
    }

    public function createAction()
    {
        $form = $this->createForm(new SimplePostForm()
                , new SimplePost($this->getAuthor())
                , ['action' => $this->generateUrl('simplepost_create')]
        );

        return $this->processForm($form);
    }

    public function editAction($id)
    {
        $repo = $this->getRepository();
        $post = $repo->findByPk($id);

        $form = $this->createForm(new SimplePostForm()
                , $post
                , ['action' => $this->generateUrl('simplepost_edit', ['id' => $id])]
        );

        return $this->processForm($form);
    }

    public function deleteAction($id)
    {
        try {
            $coll = $this->getCollection();
            $coll->remove(['_id' => new \MongoId($id)]);
            $this->pushFlash('notice', 'Message deleted');
        } catch (\MongoException $e) {
            $this->pushFlash('warning', 'Message not deleted');
        }

        return $this->redirectRouteOk('content_index');
    }

}