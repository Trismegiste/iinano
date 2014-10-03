<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

/**
 * AbusiveController is a controller for managing abusive content repoorts
 */
class AbusiveController extends ContentController
{

    public function reportPublishAction($id, $action, $wallNick, $wallFilter)
    {
        $doc = $this->getRepository()->findByPk($id);
        switch ($action) {
            case 'add':
                $doc->report($this->getAuthor());
                $this->pushFlash('notice', 'You have reported this content as abusive');
                break;
            default:
                $this->createNotFoundException("Action $action");
        }

        $this->getRepository()->persist($doc);

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
    }

    public function reportListingAction()
    {
        //$this->render($view, $parameters)
    }

}
