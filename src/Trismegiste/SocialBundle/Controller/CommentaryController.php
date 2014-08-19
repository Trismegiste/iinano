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
        $form = $this->createForm(new CommentaryForm(), new Commentary($this->getAuthor()));

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $pub = $this->getRepository()->findByPk($id);

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

        return $this->render('TrismegisteSocialBundle:Content:simplepost_form.html.twig', ['form' => $form->createView()]);
    }

}