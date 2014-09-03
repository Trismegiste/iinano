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
        return $this->redirectRouteOk('wall_index', [
                    'wallNick' => $this->getUser()->getUsername(),
                    'wallFilter' => 'all'
        ]);
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

    protected function checkOwningRight(Content $post)
    {
        if (!$this->get('security.context')->isGranted('OWNER', $post)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
    }

    public function wallAction($wallNick, $wallFilter)
    {
        return $this->renderWall($wallNick, $wallFilter, 'TrismegisteSocialBundle:Content:index.html.twig');
    }

    /**
     * Render of index.html.twig or a subclass
     * 
     * @param string $wallNick the nickname of the currently viewed wall
     * @param type $wallFilter the filter of the currently viewed wall
     * @param type $wallSubview the twig template
     * @param array $parameters other parameters
     * 
     * @return Response
     * @throws NotFoundHttpException
     */
    protected function renderWall($wallNick, $wallFilter, $wallSubview, array $parameters = [])
    {
        // filling the wall user (logged user or not)
        if ($wallNick === $this->getUser()->getUsername()) {
            $parameters['wallNick'] = $wallNick;
            $parameters['wallUser'] = $this->getUser();
        } else {
            $repo = $this->get('social.netizen.repository');
            $user = $repo->findByNickname($wallNick);
            if (is_null($user)) {
                throw new NotFoundHttpException("$wallNick does not exists");
            }
            $parameters['wallUser'] = $user;
            $parameters['wallNick'] = $user->getUsername();
        }
        // filling the wall filter
        $parameters['wallFilter'] = $wallFilter;

        // filling feed entries and skipping one if in CRUD
        $repo = $this->getRepository();
        $it = $repo->findWallEntries($parameters['wallUser'], $wallFilter, 0, $this->getPagination());

        // do we need to skip a record because it is currently edited ?
        if (array_key_exists('skipped_pub', $parameters)) {
            $it = new SkippableIterator($it, [$parameters['skipped_pub']]);
        }

        $parameters['listing'] = $it;
        $parameters['pagination'] = $this->getPagination();

        return $this->render($wallSubview, $parameters);
    }

}