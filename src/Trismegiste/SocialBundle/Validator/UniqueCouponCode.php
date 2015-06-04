<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * UniqueCouponCode is a for ensuring uniqueness of coupon hashkey
 */
class UniqueCouponCode extends Constraint
{

    public $message = 'This coupon code "%string%" is already used';

    public function validatedBy()
    {
        return 'unique_coupon_code';
    }

}
