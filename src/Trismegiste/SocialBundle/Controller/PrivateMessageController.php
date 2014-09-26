<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\Socialist\PrivateMessage;

/**
 * PrivateMessageController is a CRUD controller for Private Message
 */
class PrivateMessageController extends Template
{

    public function createAction($author)
    {
        return $this->render('TrismegisteSocialBundle:PrivateMessage:create_form.html.twig', []);
    }

}
