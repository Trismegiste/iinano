<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;

/**
 * CouponController is a crud controller for coupons
 */
class CouponController extends Template
{

    public function listingAction()
    {
        $form = $this->createForm(new CouponType());

        $param = [
            'form' => $form->createView()
        ];
        return $this->render('TrismegisteSocialBundle:Admin/Coupon:listing.html.twig', $param);
    }

}
