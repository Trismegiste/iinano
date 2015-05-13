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

    protected function getDefaultParam()
    {
        $parameters['wallNick'] = $this->getUser()->getUsername();
        $parameters['wallUser'] = $this->getUser();
        $parameters['wallFilter'] = 'self';
        $parameters['pagination'] = $this->getPagination();
        $parameters['commentary_preview'] = $this->getParameter('social.commentary_preview');

        return $parameters;
    }

    public function listingAction(Request $req)
    {
        $parameters = $this->getDefaultParam();

        $search = $req->query->get('keyword');
        $parameters['keyword'] = $search;

        $repo = $this->get('dokudoki.repository');
        $parameters['listing'] = $repo->find(['$text' => ['$search' => $search]])
                ->limit($this->getPagination());

        return $this->render('TrismegisteSocialBundle:Content:search.html.twig', $parameters);
    }

    public function ajaxSearchMoreAction($offset, $keyword)
    {
        $this->onlyAjaxRequest();

        $parameters = $this->getDefaultParam();
        $parameters['keyword'] = $keyword;

        $repo = $this->get('dokudoki.repository');
        $parameters['listing'] = $repo->find(['$text' => ['$search' => $keyword]])
                ->offset($offset)
                ->limit($this->getPagination());

        return $this->render('TrismegisteSocialBundle:Content:ajax/index_more.html.twig', $parameters);
    }

}
