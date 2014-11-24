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
        $repo = $this->get('social.publishing.repository');

        switch ($action) {
            case 'add':
                $repo->iReportThat($id);
                $this->pushFlash('notice', 'You have reported this content as abusive');
                break;

            case 'delete':
                $repo->iCancelReport($id);
                $this->pushFlash('notice', 'You have removed your report on this content');
                break;
        }

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
    }

    public function listingAction()
    {
        $reportRepo = $this->get('social.abusereport.repository');
        $reportRepo->compileReport();
        $iterator = $reportRepo->findMostReported(0, 30);

        return $this->render('TrismegisteSocialBundle:AbuseReport:index.html.twig', [
                    'listing' => $iterator
        ]);
    }

    public function sendOnCommentaryAction($id, $uuid, $action, $wallNick, $wallFilter)
    {
        $repo = $this->get('social.commentary.repository');

        switch ($action) {
            case 'add':
                $repo->iReportThat($id, $uuid);
                $this->pushFlash('notice', 'You have reported this comment as abusive');
                break;

            case 'delete':
                $repo->iCancelReport($id, $uuid);
                $this->pushFlash('notice', 'You have removed your report on this comment');
                break;
        }

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
    }

}
