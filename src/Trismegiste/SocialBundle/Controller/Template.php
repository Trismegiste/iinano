<?php

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Design pattern : Template Method
 *
 * This is a template for a controller for the default rendering of a 
 * twitter bootstrap layout (white w/ simple black top menu)
 */
abstract class Template extends Controller
{

    public function getDoctrine()
    {
        throw new \LogicException('Doctrine is not here');
    }

    protected function getRepo()
    {
        return $this->get('repository.vertex');
    }

    protected function getCollection()
    {
        return $this->get('dokudoki.collection');
    }

    public function aboutAction()
    {
        return $this->render('TrismegisteSocialBundle:Default:about.html.twig');
    }

    protected function getTopMenu()
    {
        return [
            'About' => 'trismegiste_about'
        ];
    }

    /**
     * Action for the homepage
     *
     * @return Response
     */
    abstract public function indexAction();

    /**
     * Adds some data to the page before its rendering
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $parameters['topmenu'] = $this->getTopMenu();

        return parent::render($view, $parameters, $response);
    }

    protected function redirectRouteOk($name, $param = [])
    {
        return $this->redirect($this->generateUrl($name, $param));
    }

}
