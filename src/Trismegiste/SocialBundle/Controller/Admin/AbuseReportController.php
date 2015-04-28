<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\AbuseReportActionType;

/**
 * AbuseReportController is a controller for administrate AbuseReport on Publishing and Commentary
 */
class AbuseReportController extends Template
{

    public function pubListingAction()
    {
        $reportRepo = $this->get('social.abusereport.repository');
        $iterator = $reportRepo->findMostReportedPublish(0, 30);
        $form = $this->createForm(new AbuseReportActionType($iterator));

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            print_r($form->getData());
        }

        return $this->render('TrismegisteSocialBundle:Admin:AbuseReport/pub_listing.html.twig', [
                    'listing' => $iterator,
                    'form' => $form->createView()
        ]);
    }

    public function commListingAction()
    {
        $reportRepo = $this->get('social.abusereport.repository');
        $iterator = $reportRepo->findMostReportedCommentary(0, 30);
        $form = $this->createForm(new AbuseReportActionType($iterator));

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            print_r($form->getData());
        }

        return $this->render('TrismegisteSocialBundle:Admin:AbuseReport/comm_listing.html.twig', [
                    'listing' => $iterator,
                    'form' => $form->createView()
        ]);
    }

    /**
     * Moderator decides abuse reports on a publish are irrelevant
     *
     * @param type $id
     */
    public function resetAbuseReportOnPublishAction($id)
    {
        //@todo
    }

    /**
     * Moderator decides abuse reports on a commentary are irrelevant
     *
     * @param type $id
     * @param type $idComm
     */
    public function resetAbuseReportOnCommentaryAction($id, $idComm)
    {
        // @todo
    }

}
