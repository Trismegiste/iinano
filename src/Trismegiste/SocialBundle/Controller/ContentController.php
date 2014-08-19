<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * ContentController is a template for the wall/dashboard
 *
 * The user NEEDS to be authenticated
 */
class ContentController extends Template
{

    public function indexAction()
    {
        return $this->render('TrismegisteSocialBundle:Content:index.html.twig', []);
    }

    public function render($view, array $parameters = array(), Response $response = null)
    {
        $repo = $this->getRepository();
        $it = $repo->find([]);

        $parameters['listing'] = $it;

        return parent::render($view, $parameters, $response);
    }

}