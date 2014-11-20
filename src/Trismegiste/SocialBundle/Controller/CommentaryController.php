<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

/**
 * CommentaryController is a controller for managing Commentary
 */
class CommentaryController extends ContentController
{

    public function addOnPublishingAction($id, $wallNick, $wallFilter)
    {
        $pub = $this->get('social.publishing.repository')->findByPk($id);
        // antiflood :
        // @todo need an API in the model Publishing::isLastCommenter(AuthorInterface)
        $it = $pub->getCommentaryIterator();
        if ($it->count() > 0) {
            // @todo need an API Author::isEqualTo(AuthorInterface)
            if ($it->current()->getAuthor()->getNickname() === $this->getUser()->getUsername()) {
                $this->pushFlash('warning', 'You cannot comment until someone else adds a commentary (antiflood)');
                return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
            }
        }

        $form = $this->createForm('social_commentary');

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $newPost = $form->getData();
            try {
                $this->get('social.commentary.repository')->attachAndPersist($pub, $newPost);
                $this->pushFlash('notice', 'Commentary saved');

                return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save message');
            }
        }

        $param = [
            'publishing' => $pub,
            'skipped_pub' => $id,
            'form' => $form->createView()
        ];

        return $this->renderWall($wallNick, $wallFilter, 'TrismegisteSocialBundle:Content:commentary_form.html.twig', $param);
    }

    public function editOnPublishingAction($id, $uuid, $wallNick, $wallFilter)
    {
        $pub = $this->get('social.publishing.repository')->findByPk($id);
        $commentary = $this->get('social.commentary.repository')->findByUuid($pub, $uuid);

        $form = $this->createForm('social_commentary', $commentary);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            try {
                $this->get('social.commentary.repository')->persist($pub, $commentary);
                $this->pushFlash('notice', 'Commentary saved');

                return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], "anchor-$id-$uuid");
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save message');
            }
        }

        $param = [
            'publishing' => $pub,
            'skipped_pub' => $id,
            'form' => $form->createView()
        ];

        return $this->renderWall($wallNick, $wallFilter, 'TrismegisteSocialBundle:Content:commentary_form.html.twig', $param);
    }

    public function deleteOnPublishingAction($id, $uuid, $wallNick, $wallFilter)
    {
        $pub = $this->get('social.publishing.repository')->findByPk($id);

        try {
            $this->get('social.commentary.repository')->detachAndPersist($pub, $uuid);
            $this->pushFlash('notice', 'Commentary deleted');
        } catch (\MongoException $e) {
            $this->pushFlash('warning', 'Cannot delete commentary');
        }

        return $this->redirectRouteOk('wall_index', ['wallNick' => $wallNick, 'wallFilter' => $wallFilter], 'anchor-' . $id);
    }

}
