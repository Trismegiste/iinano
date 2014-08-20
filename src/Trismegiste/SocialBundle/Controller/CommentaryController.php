<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Form\CommentaryForm;
use Trismegiste\Socialist\Commentary;

/**
 * CommentaryController is a controller for managing Commentary
 */
class CommentaryController extends ContentController
{

    public function addOnPublishingAction($id)
    {
        $pub = $this->getRepository()->findByPk($id);
        $form = $this->createForm(new CommentaryForm(), new Commentary($this->getAuthor()));

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {

            $newPost = $form->getData();
            $pub->attachCommentary($newPost);

            try {
                $this->getRepository()->persist($pub);
                $this->pushFlash('notice', 'Message saved');
                return $this->redirectRouteOk('content_index', [], 'anchor-' . $id);
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save message');
            }
        }

        $param = [
            'publishing' => $pub,
            'skipped_pub' => $id,
            'form' => $form->createView()
        ];

        return $this->render('TrismegisteSocialBundle:Content:commentary_form.html.twig', $param);
    }

    public function editOnPublishingAction($id, $uuid)
    {
        $pub = $this->getRepository()->findByPk($id);
        $commentary = $pub->getCommentaryByUuid($uuid);

        $form = $this->createForm(new CommentaryForm(), $commentary);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            try {
                $this->getRepository()->persist($pub);
                $this->pushFlash('notice', 'Message saved');
                return $this->redirectRouteOk('content_index', [], 'anchor-' . $id);
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save message');
            }
        }

        $param = [
            'publishing' => $pub,
            'skipped_pub' => $id,
            'form' => $form->createView()
        ];

        return $this->render('TrismegisteSocialBundle:Content:commentary_form.html.twig', $param);
    }

}