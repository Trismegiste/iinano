<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * NetizenController is a controller for administrating Netizen
 */
class NetizenController extends Template
{

    public function listingAction()
    {
        $repo = $this->get('social.netizen.repository');
        $it = $repo->search();

        return $this->render('TrismegisteSocialBundle:Admin:Netizen/listing.html.twig', [
                    'listing' => $it
        ]);
    }

}
