<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\AbuseReportActionType;
use Trismegiste\SocialBundle\Utils\UnwindCommentaryIterator;

/**
 * AbuseReportController is a controller for administrate AbuseReport on Publishing and Commentary
 */
class AbuseReportController extends Template
{

    public function pubListingAction()
    {
        /* @var $reportRepo \Trismegiste\SocialBundle\Repository\AbuseReport */
        $reportRepo = $this->get('social.abusereport.repository');
        $iterator = $reportRepo->findMostReportedPublish(0, 30);
        $form = $this->createForm(new AbuseReportActionType($iterator));

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $data = $form->getData();
            switch ($data['action']) {
                case 'RESET' :
                    try {
                        $reportRepo->batchResetCounterPublish($data['selection_list']);
                        return $this->redirectRouteOk('admin_abusive_pub_listing');
                    } catch (\MongoException $e) {
                        $this->pushFlash('warning', 'Cannot reset counters, please try again');
                    }
                    break;

                case 'DELETE' :
                    try {
                        $reportRepo->batchDeletePublish($data['selection_list']);
                        return $this->redirectRouteOk('admin_abusive_pub_listing');
                    } catch (\MongoException $e) {
                        $this->pushFlash('warning', 'Cannot delete content, please try again');
                    }
                    break;
            }
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
        $form = $this->createForm(new AbuseReportActionType(new UnwindCommentaryIterator($iterator)));

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $data = $form->getData();
            switch ($data['action']) {
                case 'RESET' :
                    try {
                        $reportRepo->batchResetCounterCommentary($data['selection_list']);
                        return $this->redirectRouteOk('admin_abusive_comm_listing');
                    } catch (\MongoException $e) {
                        $this->pushFlash('warning', 'Cannot reset counters, please try again');
                    }
                    break;

                case 'DELETE' :
                    try {
                        $reportRepo->batchDeleteCommentary($data['selection_list']);
                        return $this->redirectRouteOk('admin_abusive_comm_listing');
                    } catch (\MongoException $e) {
                        $this->pushFlash('warning', 'Cannot delete commentary, please try again');
                    }
                    break;
            }
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
