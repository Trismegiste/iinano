<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

/**
 * PureController is a ...
 */
class PureController extends ContentController
{

    public function indexAction()
    {
        return $this->render('TrismegisteSocialBundle::base.html.twig', [
                    'wallUser' => $this->getUser()
        ]);
    }

}
