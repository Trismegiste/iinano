<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\CouponType;
use Symfony\Component\HttpFoundation\Request;

/**
 * CouponController is a crud controller for coupons
 */
class CouponController extends Template
{

    public function listingAction()
    {
        $listing = $this->get('dokudoki.repository')
                        ->find(['-class' => 'coupon'])->sort(['_id' => -1]);

        return $this->render('TrismegisteSocialBundle:Admin/Coupon:listing.html.twig', ['listing' => $listing]);
    }

    public function createAction(Request $request)
    {
        $form = $this->createForm(new CouponType(), null, [
            'action' => $this->generateUrl('admin_coupon_create')
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $coupon = $form->getData();
            $repo = $this->get('dokudoki.repository');
            try {
                $repo->persist($coupon);
                $this->pushFlash('notice', 'Coupon saved');

                return $this->redirectRouteOk('admin_coupon_listing');
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Could not save the new coupon');
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin/Coupon:form.html.twig', ['form' => $form->createView()]);
    }

    public function editAction($id)
    {
        $repo = $this->get('dokudoki.repository');
        $coupon = $repo->findByPk($id);
        $form = $this->createForm(new CouponType(), $coupon, [
            'action' => $this->generateUrl('admin_coupon_edit', ['id' => $id])
        ]);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $coupon = $form->getData();
            try {
                $repo->persist($coupon);
                $this->pushFlash('notice', 'Coupon saved');

                return $this->redirectRouteOk('admin_coupon_listing');
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Could not save the coupon');
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin/Coupon:form.html.twig', ['form' => $form->createView()]);
    }

    public function deleteAction($id)
    {
        $obj = $this->get('dokudoki.repository')->findByPk($id);
        if ($obj instanceof \Trismegiste\SocialBundle\Ticket\Coupon) {
            $this->get('dokudoki.collection')->remove(['_id' => $obj->getId()]);

            return new \Symfony\Component\HttpFoundation\Response('', 200);
        }
    }

}
