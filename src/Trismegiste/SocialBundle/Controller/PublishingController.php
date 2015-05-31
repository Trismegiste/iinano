<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\Form\Form;

/**
 * PublishingController is the controller for CRUD actions on Publishing subclasses
 */
class PublishingController extends ContentController
{

    protected function processForm($type, Form $form)
    {
        $repo = $this->get('social.publishing.repository');

        $form->handleRequest($this->getRequest());
        // remove the current edited entity from the listing
        if (!is_null($form->getData())) {
            $param['skipped_pub'] = $form->getData()->getId();
        }
        if ($form->isValid()) {
            $newPost = $form->getData();
            try {
                $repo->persist($newPost);
                $this->pushFlash('notice', 'Message saved');

                return $this->redirectRouteOk('wall_index', [
                            'wallNick' => $this->getUser()->getUsername(),
                            'wallFilter' => 'self'
                                ], 'anchor-' . $newPost->getId());
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save message');
            }
        }

        $param['form'] = $form->createView();
        $template = "TrismegisteSocialBundle:Content:form/$type.html.twig";
        return $this->renderWall($this->getUser()->getUsername()
                        , 'self', $template
                        , $param);
    }

    public function createAction($type)
    {
        $form = $this->createForm('social_' . $type, null, [
            'action' => $this->generateUrl('publishing_create', ['type' => $type])
        ]);

        return $this->processForm($type, $form);
    }

    public function editAction($id)
    {
        $repo = $this->get('social.publishing.repository');
        $post = $repo->findByPk($id);
        $type = $repo->getClassAlias($post);

        $form = $this->createForm('social_' . $type, $post, [
            'action' => $this->generateUrl('publishing_edit', ['id' => $id])
        ]);

        return $this->processForm($type, $form);
    }

    public function deleteAction($id)
    {
        try {
            $repo = $this->get('social.publishing.repository');
            $repo->delete($id, $this->getCollection());
            $this->pushFlash('notice', 'Message deleted');
        } catch (\MongoException $e) {
            $this->pushFlash('warning', 'Message not deleted');
        }

        return $this->redirectRouteOk('wall_index', [
                    'wallNick' => $this->getUser()->getUsername(),
                    'wallFilter' => 'self'
        ]);
    }

    public function showAction($id)
    {
        $repo = $this->get('social.publishing.repository');
        try {
            $post = $repo->findByPk($id);

            $wallUser = $this->getUser();
            $wallNick = $wallUser->getUsername();
            $param = [
                'publishing' => $post,
                'wallNick' => $wallNick,
                'wallUser' => $wallUser,
                'wallFilter' => 'self',
                'pagination' => $this->getPagination()
            ];
        } catch (\Trismegiste\Yuurei\Persistence\NotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $this->render('TrismegisteSocialBundle:Content:publishing_permalink.html.twig'
                        , $param);
    }

}
