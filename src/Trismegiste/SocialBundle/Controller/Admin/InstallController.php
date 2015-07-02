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
 * InstallController controls the first installation of the app
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
            try {
                $config['oauth_provider'] = $form->getData();
                $repo->write($config);
                $this->pushFlash('notice', 'The configuration has been saved, you can now create your admin account.');

                return $this->redirectRouteOk('dynamic_config_create');
            } catch (\Exception $e) {
                $this->pushFlash('warning', 'Cannot write configuration');
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin:install.html.twig', [
                    'install' => $form->createView(),
                    'providerCount' => count($repo->all())
        ]);
    }

}
