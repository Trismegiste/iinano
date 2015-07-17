<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\DynamicCfgType;
use Trismegiste\SocialBundle\Form\InstallParamType;

/**
 * AdminController is a controller for a global admin
 */
class AdminController extends Template
{

    public function dashboardAction()
    {
        $coll = $this->get('dokudoki.collection');
        $netRepo = $this->get('social.netizen.repository');
        $ticketRepo = $this->get('social.ticket.repository');
        $param = [
            'allUser' => $netRepo->countAllUser(),
            'userOverLast24h' => $netRepo->countOnLastPeriod(1),
            'userOverLastWeekPerDay' => $netRepo->countOnLastPeriod(7) / 7.0,
            'userOverLastMonthPerDay' => $netRepo->countOnLastPeriod(30) / 30.0,
            'userOverLastYearPerDay' => $netRepo->countOnLastPeriod(365) / 365.0,
            'conversionRate' => $ticketRepo->getConversionRate(),
            'renewalRate' => $ticketRepo->getRenewalRate(),
            'feeOverLastMonth' => $this->getFeeTotalOver(30),
            'feeOverLastYear' => $this->getFeeTotalOver(365),
            'allFee' => $this->getFeeTotalOver(),
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

    public function editOAuthProviderKeyPairAction()
    {
        $repo = $this->get('social.dynamic_config');
        $config = $repo->read(true);
        $form = $this->createForm(new InstallParamType(), $config['oauth_provider']);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            try {
                $config['oauth_provider'] = $form->getData();
                $repo->write($config);
                $this->pushFlash('notice', 'OAuth providers saved');

                // return to the same page
                return $this->redirectRouteOk('admin_oauthprovider_edit');
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save OAuth config');
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin:dynamic_config_form.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    public function editPaymentConfigAction()
    {
        $repo = $this->get('social.dynamic_config');
        $config = $repo->read(true);
        $form = $this->createForm(new \Trismegiste\SocialBundle\Form\PaypalType(), $config['paypal']);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            try {
                $config['paypal'] = $form->getData();
                $repo->write($config);
                $this->pushFlash('notice', 'Paypal gateway config saved');

                // return to the same page
                return $this->redirectRouteOk('admin_paymentgateway_edit');
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot save Paypal config');
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin:dynamic_config_form.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    protected function getFeeTotalOver($periodInDay = null)
    {
        /* @var $coll \MongoCollection */
        $coll = $this->get('dokudoki.collection');

        $filterPeriod = ['ticket.purchase.-class' => 'fee'];
        if (!is_null($periodInDay)) {
            $filterPeriod['ticket.purchasedAt'] = ['$gte' => new \MongoDate(time() - 86400 * $periodInDay)];
        }

        $iter = $coll->aggregateCursor([
            ['$match' => ['-class' => 'netizen']],
            ['$unwind' => '$ticket'],
            ['$project' => ['ticket' => true]],
            ['$match' => $filterPeriod],
            ['$group' => ['_id' => '$ticket.purchase.currency', 'total' => ['$sum' => '$ticket.purchase.amount']]]
        ]);

        return $iter;
    }

}
