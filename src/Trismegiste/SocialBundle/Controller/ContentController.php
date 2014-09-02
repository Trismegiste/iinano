<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trismegiste\Socialist\Content;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Trismegiste\SocialBundle\Utils\SkippableIterator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ContentController is a template for the wall/dashboard
 *
 * The user NEEDS to be authenticated
 */
class ContentController extends Template
{

    protected function getPagination()
    {
        // @todo add a config somewhere
        return 20;
    }

    public function indexAction()
    {
        return $this->render('TrismegisteSocialBundle:Content:index.html.twig', []);
    }

    public function ajaxMoreAction($offset)
    {
        // @todo problem with dynamic tooltip
        // @todo url is not valid when there is a filter : session ?
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new AccessDeniedException('U haxxor');
        }

        $repo = $this->getRepository();
        $parameters['listing'] = $repo->findLastEntries($offset, $this->getPagination());

        return parent::render('TrismegisteSocialBundle:Content:index_more.html.twig', $parameters);
    }

    public function render($view, array $parameters = array(), Response $response = null)
    {
        $repo = $this->getRepository();
        $it = $repo->findLastEntries(0, $this->getPagination());

        // do we need to skip a record because it is currently edited ?
        if (array_key_exists('skipped_pub', $parameters)) {
            $it = new SkippableIterator($it, [$parameters['skipped_pub']]);
        }
        // do we need to feed the current user (default = logged)
        if (!array_key_exists('current_user', $parameters)) {
            $parameters['current_user'] = $this->getUser();
        }

        $parameters['listing'] = $it;
        $parameters['pagination'] = $this->getPagination();

        return parent::render($view, $parameters, $response);
    }

    protected function checkOwningRight(Content $post)
    {
        if (!$this->get('security.context')->isGranted('OWNER', $post)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
    }

    public function filterAction($center, $author, $offset)
    {
        // @todo filter content based on :
        // * current author vertex (me or someone else) => the content of the navbar
        // * type of edge (himself, following, follower, friends, all)
        // => must be stateless & default === index

        $repo = $this->get('social.netizen.repository');
        $user = $repo->findByNickname($center);
        if (is_null($user)) {
            throw new NotFoundHttpException("$center does not exists");
        }
        $parameters['current_user'] = $user;

        // now filter on type of author :
        switch ($author) {
            case 'self':
                $filterAuthor = new \ArrayIterator([$user->getAuthor()]);
                break;

            case 'following':
                $filterAuthor = $user->getFollowingIterator();
                break;

            case 'follower':
                $filterAuthor = $user->getFollowerIterator();
                break;

            case 'friend':
                $filterAuthor = $user->getFriendIterator();
                break;

            default:
                $filterAuthor = null;
        }

        $parameters['listing'] = $this->getRepository()
                ->findLastEntries($offset, $this->getPagination(), $filterAuthor);
        $parameters['pagination'] = $this->getPagination();

        return parent::render('TrismegisteSocialBundle:Content:index.html.twig', $parameters);
    }

}