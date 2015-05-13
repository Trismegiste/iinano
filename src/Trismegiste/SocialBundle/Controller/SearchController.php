<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * SearchController is a controller for text search through Publishing documents
 */
class SearchController extends ContentController
{

    public function listingAction(Request $req)
    {
        $search = $req->query->get('keyword');
        $parameters['keyword'] = $search;

        // filling the logged user info for wall
        $parameters['wallNick'] = $this->getUser()->getUsername();
        $parameters['wallUser'] = $this->getUser();
        $parameters['wallFilter'] = 'self';

        // filling feed entries and skipping one if in CRUD
        $repo = $this->get('dokudoki.repository');
        $it = $repo->find(['$text' => ['$search' => $search]])->limit($this->getPagination());

        $parameters['listing'] = $it;
        $parameters['pagination'] = $this->getPagination();
        $parameters['commentary_preview'] = $this->getParameter('social.commentary_preview');

        return $this->render('TrismegisteSocialBundle:Content:search.html.twig', $parameters);
    }

}
