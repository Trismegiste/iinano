<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\Socialist\PrivateMessage;
use Trismegiste\SocialBundle\Form\PrivateMessageType;

/**
 * PrivateMessageController is a CRUD controller for Private Message
 */
class PrivateMessageController extends Template
{

    public function createAction($author)
    {
        $repo = $this->get('social.private_message.repository');

        $target = $this->get('social.netizen.repository')
                ->findByNickname($author)
                ->getAuthor();

        $newMessage = $repo->createNewMessageTo($target);
        $form = $this->createForm(new PrivateMessageType(), $newMessage, [
            'action' => $this->generateUrl('private_create', ['author' => $author
            ])
        ]);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $repo->persist($form->getData());
        }

        return $this->render('TrismegisteSocialBundle:PrivateMessage:create_form.html.twig', [
                    'form' => $form->createView(),
                    'listing' => []
        ]);
    }

}
