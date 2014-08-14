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

        return $this->render('TrismegisteSocialBundle:Vertex:index.html.twig', ['vertex' => $it]);
    }

    public function aboutAction()
    {
        return $this->render('TrismegisteSocialBundle:Default:about.html.twig');
    }

}