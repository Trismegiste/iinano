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
        return $this->render('TrismegisteSocialBundle:Admin/Coupon:listing.html.twig');
    }

}
