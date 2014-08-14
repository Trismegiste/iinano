<?php

namespace Trismegiste\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

    protected function getRepository()
    {
        return $this->get('dokudoki.repository');
    }

    protected function getCollection()
    {
        return $this->get('dokudoki.collection');
    }

    protected function redirectRouteOk($name, $param = [])
    {
        return $this->redirect($this->generateUrl($name, $param));
    }

}
