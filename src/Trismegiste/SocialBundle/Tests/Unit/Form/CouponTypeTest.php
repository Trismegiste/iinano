<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\CouponType;
use Trismegiste\SocialBundle\Ticket\Coupon;

/**
 * CouponTypeTest tests the CouponType form
 */
class CouponTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new CouponType();
    }

    public function getInvalidInputs()
    {
        $obj = new Coupon();
        return [
            [['hashKey' => 'a'], $obj, ['hashKey']]
        ];
    }

    public function getValidInputs()
    {
        $expi = new \DateTime('tomorrow');
        $obj = new Coupon();
        $obj->setDurationValue(20);
        $obj->hashKey = 'AZERTY';
        $obj->maximumUse = 10;
        $obj->expiredAt = $expi;
        return [
            [['durationValue' => 20, 'hashKey' => 'AZERTY', 'maximumUse' => 20, 'expiredAt' => $expi], $obj]
        ];
    }

}
