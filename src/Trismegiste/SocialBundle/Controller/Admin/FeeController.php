<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\EntranceFeeType;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;

/**
 * FeeController is a controller for editing the entrance fee of the social network
 */
class FeeController extends Template
{

    /**
     * Since there's one and only one instance of EntranceFee
     * This controller creates/edits this entity
     */
    public function editAction()
    {
        $repo = $this->get('dokudoki.repository');
        $fee = $repo->findOne([MapAlias::CLASS_KEY => 'fee']);
        $form = $this->createForm(new EntranceFeeType(), $fee);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $newFee = $form->getData();
            try {
                $repo->persist($newFee);
                $this->pushFlash('notice', 'Entrance fee saved');

                // return to the same page
                $this->redirectRouteOk('admin_entrancefee_edit');
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save entrance fee');
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin:fee_form.html.twig', [
                    'form' => $form->createView()
        ]);
    }

}
