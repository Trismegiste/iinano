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
        $form = $this->createForm('social_private_message', null, [
            'action' => $this->generateUrl('private_create')
        ]);

        if (!empty($author)) {
            $form->get('target')->setData(new \Trismegiste\Socialist\Author($author));
        }

        $repo = $this->get('social.private_message.repository');

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $repo->persist($form->getData());
            $this->pushFlash('notice', 'Message sent');

            return $this->redirectRouteOk('private_create');
        }

        return $this->render('TrismegisteSocialBundle:PrivateMessage:create_form.html.twig', [
                    'form' => $form->createView(),
                    'received' => $repo->findAllReceived(),
                    'sent' => $repo->findAllSent()
        ]);
    }

    public function ajaxFindFollowerAction(Request $request)
    {
        $choice = [];
        $nick = $request->query->get('q');
//        $cursor = $this->get('dokudoki.repository')
//                ->find(['-class' => 'netizen', 'author.nickname' => new \MongoRegex("/$nick/")]);
//        foreach ($cursor as $user) {
//            $choice[] = $user->getUsername();
//        }

        $iter = $this->getUser()->getFollowerIterator();
        foreach ($iter as $key => $dummy) {
            if (preg_match("#$nick#", $key)) {
                $choice[] = $key;
            }
        }

        return new \Symfony\Component\HttpFoundation\JsonResponse($choice);
    }

}
