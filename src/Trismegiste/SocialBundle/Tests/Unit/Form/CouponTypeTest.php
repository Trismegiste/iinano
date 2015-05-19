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

    public function createData()
    {
        $obj = new Coupon();
        $obj->expiredAt = new \DateTime('2015-01-01');
    }

    public function getInvalidInputs()
    {
        $obj = new Coupon();
        $obj->hashKey = 'a';
        $obj->maximumUse = 0;
        $obj->setDurationValue(-5);
        $obj->expiredAt = new \DateTime('2015-01-01');
        return [
            [
                ['hashKey' => 'a', 'maximumUse' => 0, 'durationValue' => -5, 'expiredAt' => ['year' => 2015, 'month' => 1, 'day' => 1]],
                $obj,
                ['hashKey', 'maximumUse', 'durationValue']
            ]
        ];
    }

    public function getValidInputs()
    {
        $obj = new Coupon();
        $obj->setDurationValue(5);
        $obj->hashKey = 'AZERTY';
        $obj->maximumUse = 50;
        $obj->expiredAt = new \DateTime('2015-05-05');
        return [
            [
                [
                    'durationValue' => 5,
                    'hashKey' => 'AZERTY',
                    'maximumUse' => 50,
                    'expiredAt' => ['year' => 2015, 'month' => 5, 'day' => 5]
                ],
                $obj
            ]
        ];
    }

}
