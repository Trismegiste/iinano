<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Form\CommentaryType;
use Trismegiste\Socialist\Commentary;

/**
 * CommentaryController is a controller for managing Commentary
 */
class CommentaryController extends ContentController
{

    public function addOnPublishingAction($id, $wallNick, $wallFilter)
    {
        $pub = $this->getRepository()->findByPk($id);
        $form = $this->createForm(new CommentaryType(), new Commentary($this->getAuthor()));

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {

            $newPost = $form->getData();
            $pub->attachCommentary($newPost);

            try {
                $this->getRepository()->persist($pub);
                $this->pushFlash('notice', 'Commentary saved');

                return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save message');
            }
        }

        $param = [
            'publishing' => $pub,
            'skipped_pub' => $id,
            'form' => $form->createView()
        ];

        return $this->renderWall($wallNick, $wallFilter, 'TrismegisteSocialBundle:Content:commentary_form.html.twig', $param);
    }

    public function editOnPublishingAction($id, $uuid, $wallNick, $wallFilter)
    {
        $pub = $this->getRepository()->findByPk($id);
        $commentary = $pub->getCommentaryByUuid($uuid);

        $this->checkOwningRight($commentary);

        $form = $this->createForm(new CommentaryType(), $commentary);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            try {
                $commentary->setLastEdited(new \DateTime());
                $this->getRepository()->persist($pub);
                $this->pushFlash('notice', 'Commentary saved');

                return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], "anchor-$id-$uuid");
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save message');
            }
        }

        $param = [
            'publishing' => $pub,
            'skipped_pub' => $id,
            'form' => $form->createView()
        ];

        return $this->renderWall($wallNick, $wallFilter, 'TrismegisteSocialBundle:Content:commentary_form.html.twig', $param);
    }

    public function deleteOnPublishingAction($id, $uuid, $wallNick, $wallFilter)
    {
        $pub = $this->getRepository()->findByPk($id);
        $commentary = $pub->getCommentaryByUuid($uuid);

        $this->checkOwningRight($commentary);

        $pub->detachCommentary($commentary);
        try {
            $this->getRepository()->persist($pub);
            $this->pushFlash('notice', 'Commentary deleted');
        } catch (\MongoException $e) {
            $this->pushFlash('warning', 'Cannot delete commentary');
        }

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
    }

}