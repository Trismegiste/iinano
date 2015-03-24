<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\CouponType;

/**
 * CouponController is a crud controller for coupons
 */
class CouponController extends Template
{

    public function listingAction()
    {
        $form = $this->createForm(new CouponType());

        $param = [
            'form' => $form->createView(),
            'listing' => []
        ];
        return $this->render('TrismegisteSocialBundle:Admin/Coupon:listing.html.twig', $param);
    }

}
