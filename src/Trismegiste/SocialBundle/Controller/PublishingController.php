<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\Socialist\SimplePost;
use Trismegiste\Socialist\Commentary;
use Symfony\Component\HttpFoundation\Request;

/**
 * PublishingController is the main controller for viewing the content
 * 
 * The user NEEDS to be authenticated
 */
class PublishingController extends Template
{

    public function indexAction()
    {
        $repo = $this->getRepository();
        $it = $repo->find([]);

        $form = $this->createForm(new \Trismegiste\SocialBundle\Form\SimplePostForm()
                , new SimplePost($this->getUser()->getAuthor())
                , ['action' => $this->generateUrl('content_index')]
        );

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $newPost = $form->getData();
            $repo->persist($newPost);
        }

        $doc = [
            'listing' => $it,
            'form' => $form->createView()
        ];

        return $this->render('TrismegisteSocialBundle:Content:index.html.twig', $doc);
    }

    public function showAction(Request $request)
    {
        $pk = $request->get('pk');
        $publish = $this->getRepository()->findByPk($pk);

        $form = $this->createForm(new \Trismegiste\SocialBundle\Form\CommentaryForm()
                , new Commentary($this->getUser()->getAuthor())
                , ['action' => $this->generateUrl('publishing_show', ['pk' => $pk])]
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $comment = $form->getData();
            $publish->attachCommentary($comment);
            $this->getRepository()->persist($publish);
        }

        $content = [
            'simplepost' => $publish,
            'comment_form' => $form->createView()
        ];

        return $this->render('TrismegisteSocialBundle:Content:publishing_show.html.twig', $content);
    }

}