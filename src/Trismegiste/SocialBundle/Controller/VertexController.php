<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\Socialist\SimplePost;
use Trismegiste\Socialist\Commentary;
use Symfony\Component\HttpFoundation\Request;

/**
 * VertexController manages CRUD for Vertex
 */
class VertexController extends Template
{

    public function indexAction()
    {
        $repo = $this->getRepository();
        $it = $repo->find([]);

        $form = $this->createForm(new \Trismegiste\SocialBundle\Form\SimplePostForm()
                , new SimplePost($this->getUser()->getAuthor())
                , ['action' => $this->generateUrl('trismegiste_homepage')]
        );

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $newPost = $form->getData();
            $repo->persist($newPost);
        }

        $doc = [
            'vertex' => $it,
            'form' => $form->createView()
        ];

        return $this->render('TrismegisteSocialBundle:Vertex:index.html.twig', $doc);
    }

    public function detailAction(Request $request)
    {
        $pk = $request->get('pk');
        $publish = $this->getRepository()->findByPk($pk);

        $form = $this->createForm(new \Trismegiste\SocialBundle\Form\CommentaryForm()
                , new Commentary($this->getUser()->getAuthor())
                , ['action' => $this->generateUrl('publish_detail', ['pk' => $pk])]
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

        return $this->render('TrismegisteSocialBundle:Vertex:publish.html.twig', $content);
    }

}