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

    public function listingAction()
    {
        $reportRepo = $this->get('social.abusereport.repository');
        $reportRepo->compileReport();
        $iterator = $reportRepo->findMostReported(0, 30);

        return $this->render('TrismegisteSocialBundle:Admin:AbuseReport/listing.html.twig', [
                    'listing' => $iterator
        ]);
    }

}
