<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\PaypalType;

/**
 * Tests the PaypalType form
 */
class PaypalTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new PaypalType();
    }

    public function getInvalidInputs()
    {
        $empty = ['username' => null, 'password' => null, 'signature' => null, 'sandbox' => null];
        return [
            [[], $empty, ['username', 'password', 'signature', 'sandbox']]
        ];
    }

    public function getValidInputs()
    {
        $vec = ['username' => 'aaa', 'password' => 'bbb', 'signature' => 'ccc', 'sandbox' => true];
        return [
            [$vec, $vec]
        ];
    }

}
