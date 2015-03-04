<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * FeeController is a controller for editing the entrance fee of the social network
 */
class FeeController extends Template
{

    /**
     * Since there's one and only one instance of EntranceFee
     * It creates/edits this entity
     */
    public function editAction()
    {
        $type = new \Trismegiste\SocialBundle\Form\EntranceFeeType();
        $form = $this->createForm($type);

        return $this->render('TrismegisteSocialBundle:Admin:fee_form.html.twig', ['form' => $form->createView()]);
    }

}
