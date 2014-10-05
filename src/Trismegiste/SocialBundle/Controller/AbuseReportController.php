<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

/**
 * AbuseReportController is a controller for managing abusive/spam content reports
 */
class AbuseReportController extends ContentController
{

    public function sendOnPublishingAction($id, $action, $wallNick, $wallFilter)
    {
        $doc = $this->getRepository()->findByPk($id);
        switch ($action) {
            case 'add':
                $doc->report($this->getAuthor());
                // @todo this falsh must replace the original content
                //$this->pushFlash('notice', 'You have reported this content as abusive');
                break;
            default:
                $this->createNotFoundException("Action $action");
        }

        $this->getRepository()->persist($doc);

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
    }

    public function listingAction()
    {
        $reportRepo = $this->get('social.abusereport.repository');
        $reportRepo->compileReport();
        $iterator = $reportRepo->findMostReported(0,30);

        $listing = [];
        foreach ($iterator as $report) {
            $report['content'] = $this->getRepository()->findByPk($report['_id']['id']);
            $listing[] = $report;
        }
        return $this->render('TrismegisteSocialBundle:AbuseReport:dashboard.html.twig', [
                    'listing' => $listing
        ]);
    }

    public function sendOnCommentaryAction($id, $uuid, $action, $wallNick, $wallFilter)
    {
        $doc = $this->getRepository()->findByPk($id);
        $commentary = $doc->getCommentaryByUuid($uuid);

        switch ($action) {
            case 'add':
                $commentary->report($this->getAuthor());
                // @todo this falsh must replace the original content
                //$this->pushFlash('notice', 'You have reported this content as abusive');
                break;
            default:
                $this->createNotFoundException("Action $action");
        }

        $this->getRepository()->persist($doc);

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
    }

}
