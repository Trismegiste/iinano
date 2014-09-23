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
            $newStatus = $form->getData();
            try {
                $this->getRepository()->persist($newStatus);
                $this->pushFlash('notice', 'Status saved');

                return $this->redirectRouteOk('wall_index', [
                            'wallNick' => $this->getUser()->getUsername(),
                            'wallFilter' => 'self'
                                ], 'anchor-' . $newStatus->getId());
            } catch (\Exception $e) {
                $this->pushFlash('warning', 'Status not saved');
            }
        }

        return $this->renderWall($this->getUser()->getUsername(), 'self', 'TrismegisteSocialBundle:Content:publishing_form.html.twig', ['form' => $form->createView()]);
    }

}