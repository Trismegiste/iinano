<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * FeeController is a controller for editing the entrance fee of the social network
 */
class FeeController extends Template
{

    /**
     * Since there's one and only one instance of EntraceFee
     * It creates/edits this entity
     */
    public function editAction()
    {
        return new \Symfony\Component\HttpFoundation\Response("todo");
    }

}
