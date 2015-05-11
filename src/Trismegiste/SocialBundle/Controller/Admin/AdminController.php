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
        $coll = $this->get('dokudoki.collection');
        $param = [
            'user' => $this->get('social.netizen.repository')->countAllUser(),
            'content' => $coll->aggregateCursor([['$group' => ['_id' => '$-class', 'counter' => ['$sum' => 1]]]]),
            'health' => [
                'cpu' => sys_getloadavg(),
                'mongo' => $coll->db
                        ->execute(new \MongoCode('db.dokudoki.stats();'))['retval'],
                'memory' => memory_get_peak_usage(true)
            ]
        ];


        return $this->render('TrismegisteSocialBundle:Admin:dashboard.html.twig', $param);
    }

    public function editDynamicConfigAction()
    {
        $repo = $this->get('social.dynamic_config');
        $config = $repo->read(true);
        $form = $this->createForm(new DynamicCfgType(), $config);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $newConfig = $form->getData();
            try {
                $repo->write($newConfig);
                $this->pushFlash('notice', 'Config saved');

                // return to the same page
                return $this->redirectRouteOk('admin_dynamic_config_edit');
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save config');
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin:dynamic_config_form.html.twig', [
                    'form' => $form->createView()
        ]);
    }

}
