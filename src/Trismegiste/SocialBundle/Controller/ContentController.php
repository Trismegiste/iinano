<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trismegiste\Socialist\Content;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    public function ajaxMoreAction($offset)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new AccessDeniedException('U haxxor');
        }

        $repo = $this->getRepository();
        $it = $repo->findLastEntries();

        $parameters['listing'] = $it;

        return parent::render('TrismegisteSocialBundle:Content:index_more.html.twig', $parameters);
    }

    public function render($view, array $parameters = array(), Response $response = null)
    {
        $repo = $this->getRepository();
        $it = $repo->findLastEntries();

        if (array_key_exists('skipped_pub', $parameters)) {
            $it = new \Trismegiste\SocialBundle\Utils\SkippableIterator($it, [$parameters['skipped_pub']]);
        }
        $parameters['listing'] = $it;

        return parent::render($view, $parameters, $response);
    }

    protected function checkOwningRight(Content $post)
    {
        if (!$this->get('security.context')->isGranted('OWNER', $post)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
    }

}