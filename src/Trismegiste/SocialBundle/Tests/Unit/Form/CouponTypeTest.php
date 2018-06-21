<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use DateTime;
use Trismegiste\SocialBundle\Form\CouponType;
use Trismegiste\SocialBundle\Ticket\Coupon;
use Trismegiste\SocialBundle\Validator\UniqueCouponCodeValidator;

/**
 * CouponTypeTest tests the CouponType form
 */
class CouponTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new CouponType();
    }

    protected function createValidator()
    {
        $repo = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        return ['unique_coupon_code' => new UniqueCouponCodeValidator($repo)];
    }

    public function createData()
    {
        $obj = new Coupon();
        $obj->expiredAt = new DateTime('2015-01-01');
    }

    public function getInvalidInputs()
    {
        $obj = new Coupon();
        $obj->hashKey = 'a';
        $obj->maximumUse = 0;
        $obj->setDurationValue(-5);
        $obj->expiredAt = new DateTime();
        $obj->expiredAt->setTime(0, 0);
        return [
                [
                    ['hashKey' => 'a', 'maximumUse' => 0, 'durationValue' => -5, 'expiredAt' => ['year' => date('Y'), 'month' => date('n'), 'day' => date('d')]],
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
        $obj->expiredAt = new DateTime();
        $obj->expiredAt->setTime(0, 0);
        return [
                [
                    [
                    'durationValue' => 5,
                    'hashKey' => 'AZERTY',
                    'maximumUse' => 50,
                    'expiredAt' => ['year' => date('Y'), 'month' => date('n'), 'day' => date('d')]
                ],
                $obj
            ]
        ];
    }

}
