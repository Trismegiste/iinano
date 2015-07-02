<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\InstallParamType;

/**
 * InstallController is a ...
 */
class InstallController extends Template
{

    public function createMinimalParameterAction(Request $request)
    {
        if (0 < $this->get('social.netizen.repository')->countAllUser()) {
            throw new AccessDeniedHttpException("Already installed");
        }

        $repo = $this->get('social.dynamic_config');
        $config = $repo->read(true);
        $form = $this->createForm(new InstallParamType(), $config['oauth_provider']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $config['oauth_provider'] = $form->getData();
            $repo->write($config);

            return $this->redirectRouteOk('trismegiste_oauth_connect');
        }

        return $this->render('TrismegisteSocialBundle:Admin:install.html.twig', [
                    'install' => $form->createView()
        ]);
    }

}
