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
            'action' => $this->generateUrl('coupon_create')
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $coupon = $form->getData();
            $repo = $this->get('dokudoki.repository');
            $repo->persist($coupon);
            $this->pushFlash('notice', 'Coupon saved');

            return $this->redirectRouteOk('coupon_listing');
        } else {
            $this->pushFlash('warning', 'Coupon not saved');
        }

        return $this->render('TrismegisteSocialBundle:Admin/Coupon:create.html.twig', ['form' => $form->createView()]);
    }

}
