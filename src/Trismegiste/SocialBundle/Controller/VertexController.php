<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\Socialist\SimplePost;
use Trismegiste\Socialist\Author;

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

    public function aboutAction()
    {
        return $this->render('TrismegisteSocialBundle:Default:about.html.twig');
    }

}