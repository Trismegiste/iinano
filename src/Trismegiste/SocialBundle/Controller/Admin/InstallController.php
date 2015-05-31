<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\InstallParamType;

/**
 * InstallController is a ...
 */
class InstallController extends Template
{

    public function createMinimalParameterAction(Request $request)
    {
        if (0 !== $this->get('dokudoki.collection')->find()->count()) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException("Already installed");
        }

        $repo = $this->get('social.dynamic_config');
        $form = $this->createForm(new InstallParamType());

        $form->handleRequest($request);
        if ($form->isValid()) {
            $default = $repo->read(true);
            $default['oauth_provider'] = $form->getData();
            $repo->write($default);

            return $this->redirectRouteOk('trismegiste_login');
        }

        return $this->render('TrismegisteSocialBundle:Admin:install.html.twig', [
                    'install' => $form->createView()
        ]);
    }

}
