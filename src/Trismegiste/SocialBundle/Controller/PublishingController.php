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

    protected function processForm(Form $form)
    {
        $repo = $this->getRepository();

        $form->handleRequest($this->getRequest());
        // remove the current edited entity from the listing
        if (!is_null($form->getData()->getId())) {
            $param['skipped_pub'] = $form->getData()->getId();
        }
        if ($form->isValid()) {
            $newPost = $form->getData();
            $newPost->setLastEdited(new \DateTime());
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
        return $this->renderWall($this->getUser()->getUsername()
                        , 'self', 'TrismegisteSocialBundle:Content:publishing_form.html.twig'
                        , $param);
    }

    public function createAction($type)
    {
        $form = $this->get('social.form.factory')
                ->createCreateForm($type
                , $this->getUser()->getAuthor()  // @todo replace this by injecting the securityContext and checking ROLE_USER
                , $this->generateUrl('publishing_create', ['type' => $type]));

        return $this->processForm($form);
    }

    public function editAction($id)
    {
        $repo = $this->getRepository();
        $post = $repo->findByPk($id);

        $this->checkOwningRight($post);  // @todo replace with the injected security

        $form = $this->get('social.form.factory')
                ->createEditForm($post
                , $this->generateUrl('publishing_edit', ['id' => $id]));

        return $this->processForm($form);
    }

    public function deleteAction($id)
    {
        try {
            $repo = $this->getRepository();
            $post = $repo->findByPk($id);

            $this->checkOwningRight($post);

            // @todo this below sux alot : add a delete method in repository :
            // check rights and content class in the process
            $coll = $this->getCollection();
            $coll->remove(['_id' => new \MongoId($id)]);
            $this->pushFlash('notice', 'Message deleted');
        } catch (\MongoException $e) {
            $this->pushFlash('warning', 'Message not deleted');
        }

        return $this->redirectRouteOk('wall_index', [
                    'wallNick' => $this->getUser()->getUsername(),
                    'wallFilter' => 'self'
        ]);
    }

}
