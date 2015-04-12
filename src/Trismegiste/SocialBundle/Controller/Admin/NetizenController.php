<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * NetizenController is a controller for administrating Netizen
 */
class NetizenController extends Template
{

    public function listingAction(Request $req)
    {
        $it = [];

        if (!(empty($search = $req->query->get('search', '')))) {
            $repo = $this->get('social.netizen.repository');
            $it = $repo->search($search)->limit(5);
        }

        return $this->render('TrismegisteSocialBundle:Admin:Netizen/listing.html.twig', [
                    'listing' => $it
        ]);
    }

    public function promoteAction()
    {

    }

    public function blockAction()
    {

    }

    public function editAction($pk)
    {

    }

}
