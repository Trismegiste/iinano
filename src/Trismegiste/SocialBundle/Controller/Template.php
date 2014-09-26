<?php

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * This is a controller helper for the default rendering of a
 * zurb foundation layout (white w/ simple black top menu)
 */
abstract class Template extends Controller
{

    public function getDoctrine()
    {
        throw new \LogicException('Doctrine is not here');
    }

    /**
     * Returns the repository for published content
     *
     * @return \Trismegiste\SocialBundle\Repository\PublishingRepositoryInterface
     */
    protected function getRepository()
    {
        return $this->get('social.content.repository');
    }

    /**
     * Gets the mongo collection for this app
     *
     * @return \MongoCollection
     */
    protected function getCollection()
    {
        return $this->get('dokudoki.collection');
    }

    /**
     * Redirects to named route with its param and an optional anchor
     *
     * @param string $name
     * @param array $param
     * @param string $anchor (without the '#')
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectRouteOk($name, array $param = [], $anchor = false)
    {
        return $this->redirect($this->generateUrl($name, $param) . ($anchor ? '#' . $anchor : ''));
    }

    /**
     * Gets the current logged author
     *
     * @return \Trismegiste\Socialist\AuthorInterface
     */
    protected function getAuthor()
    {
        return $this->getUser()->getAuthor();
    }

    /**
     * Stack a flash
     *
     * @param string $type (e.g 'notice', 'warning'...)
     * @param string $msg the content of the flash
     */
    protected function pushFlash($type, $msg)
    {
        $this->get('session')->getFlashBag()->add($type, $msg);
    }

}
