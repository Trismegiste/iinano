<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * PrivateMessageController is a CRUD controller for Private Message
 */
class PrivateMessageController extends Template
{

    public function createAction($author)
    {
        $repo = $this->get('social.private_message.repository');

        $form = $this->createForm('social_private_message', null, [
            'action' => $this->generateUrl('private_create')
        ]);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $repo->persist($form->getData());
        }

        return $this->render('TrismegisteSocialBundle:PrivateMessage:create_form.html.twig', [
                    'form' => $form->createView(),
                    'listing' => $repo->findAllReceived()
        ]);
    }

    public function ajaxFindFollowerAction(Request $request)
    {
        $nick = $request->query->get('q');
        $cursor = $this->get('dokudoki.repository')
                ->find(['-class' => 'netizen', 'author.nickname' => new \MongoRegex("/$nick/")]);
        $choice = [];
        foreach ($cursor as $user) {
            $choice[] = $user->getUsername();
        }

        return new \Symfony\Component\HttpFoundation\JsonResponse($choice);
    }

}
