<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * AbuseReportController is a ...
 */
class AbuseReportController extends Template
{

    public function pubListingAction()
    {
        $reportRepo = $this->get('social.abusereport.repository');
        $iterator = $reportRepo->findMostReportedPublish(0, 30);

        return $this->render('TrismegisteSocialBundle:Admin:AbuseReport/pub_listing.html.twig', [
                    'listing' => $iterator
        ]);
    }

    public function commListingAction()
    {

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
