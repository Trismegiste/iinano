<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\DynamicCfgType;

/**
 * AdminController is a controller for a global admin
 */
class AdminController extends Template
{

    public function dashboardAction()
    {
        $param = [
            'user' => $this->get('social.netizen.repository')->countAllUser(),
            'content' => $this->get('social.publishing.repository')->countAllPublishing()
        ];

        return $this->render('TrismegisteSocialBundle:Admin:dashboard.html.twig', $param);
    }

    public function editDynamicConfigAction()
    {
        $repo = $this->get('social.dynamic_config');
        $config = $repo->read();
        $form = $this->createForm(new DynamicCfgType(), $config);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $newConfig = $form->getData();
            try {
                $repo->write($newConfig);
                $this->pushFlash('notice', 'Config saved');

                // return to the same page
                $this->redirectRouteOk('dynamic_config_edit');
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save config');
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin:dynamic_config_form.html.twig', [
                    'form' => $form->createView()
        ]);
    }

}
