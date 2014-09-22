<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\Socialist\Status;
use Trismegiste\SocialBundle\Form\StatusType;
use Symfony\Component\HttpFoundation\Request;

/**
 * StatusController is a crud controller for Statuts update
 */
class StatusController extends ContentController
{

    public function createAction(Request $request)
    {
        $form = $this->createForm(new StatusType(), new Status($this->getAuthor()));
        $form->handleRequest($request);

        if ($form->isValid()) {
            print_r($form->getData());
        }

        return $this->renderWall($this->getUser()->getUsername(), 'self', 'TrismegisteSocialBundle:Content:status_form.html.twig', ['form' => $form->createView()]);
    }

}