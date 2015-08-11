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
                case AbuseReportActionType::RESET :
                    try {
                        $reportRepo->batchResetCounterPublish($data['selection_list']);
                        $this->pushFlash('notice', count($data['selection_list']) . ' counters reset');

                        return $this->redirectRouteOk('admin_abusive_pub_listing');
                    } catch (\MongoException $e) {
                        $this->pushFlash('warning', 'Cannot reset counters, please try again');
                    }
                    break;

                case AbuseReportActionType::DELETE :
                    try {
                        $this->batchDeletePublish($data['selection_list']);
                        $this->pushFlash('notice', count($data['selection_list']) . ' contents deleted');

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
                case AbuseReportActionType::RESET :
                    try {
                        $reportRepo->batchResetCounterCommentary($data['selection_list']);
                        $this->pushFlash('notice', count($data['selection_list']) . ' counters reset');

                        return $this->redirectRouteOk('admin_abusive_comm_listing');
                    } catch (\MongoException $e) {
                        $this->pushFlash('warning', 'Cannot reset counters, please try again');
                    }
                    break;

                case AbuseReportActionType::DELETE :
                    try {
                        $reportRepo->batchDeleteCommentary($data['selection_list']);
                        $this->pushFlash('notice', count($data['selection_list']) . ' contents deleted');

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

    private function batchDeletePublish(array $selection)
    {
        /* @var $repo \Trismegiste\SocialBundle\Repository\PublishingRepositoryInterface */
        $repo = $this->get('social.publishing.repository');
        foreach ($selection as $doc) {
            $repo->deleteAdmin((string) $doc['_id']);
        }
    }

}
