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
        $default = [
            'expiredAt' => new \DateTime('tomorrow'),
            'maximumUse' => 1
        ];
        $form = $this->createForm(new CouponType(), null, [
            'action' => $this->generateUrl('coupon_create')
        ]);

        $repo = $this->get('dokudoki.repository');
        $listing = $repo->find(['-class' => 'coupon'])->sort(['_id' => -1]);

        $param = [
            'form' => $form->createView(),
            'listing' => $listing
        ];
        return $this->render('TrismegisteSocialBundle:Admin/Coupon:listing.html.twig', $param);
    }

    public function createAction(Request $request)
    {
        $form = $this->createForm(new CouponType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $coupon = $form->getData();
            $repo = $this->get('dokudoki.repository');
            $repo->persist($coupon);
            $this->pushFlash('notice', 'Coupon saved');
        } else {
            $this->pushFlash('warning', 'Coupon not saved');
        }

        return $this->redirectRouteOk('coupon_listing');
    }

}
