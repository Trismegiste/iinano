<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\EntranceFeeType;
use Trismegiste\SocialBundle\Ticket\EntranceFee;

/**
 * EntranceFeeTypeTest tests the EntranceFeeType
 */
class EntranceFeeTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new EntranceFeeType();
    }

    public function getInvalidInputs()
    {
        $obj = new EntranceFee();
        $obj->setAmount(-999);
        return [
            [['amount' => -999, 'currency' => 'ARF', 'durationValue' => 5], $obj, ['amount', 'currency', 'durationValue']]
        ];
    }

    public function getValidInputs()
    {
        $obj = new EntranceFee();
        $obj->setAmount(9.99);
        $obj->setCurrency('USD');
        $obj->setDurationValue(12);
        return [
            [['amount' => 9.99, 'currency' => 'USD', 'durationValue' => 12], $obj]
        ];
    }

}
