<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\DynamicCfgType;

/**
 * DynamicCfgTypeTest tests DynamicCfgType
 */
class DynamicCfgTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new DynamicCfgType();
    }

    public function getInvalidInputs()
    {
        return [
            [
                ['appTitle' => 'a', 'minimumAge' => 5],
                ['appTitle' => 'a', 'minimumAge' => 5, 'freeAccess' => null, 'subTitle' => null],
                ['appTitle', 'minimumAge', 'freeAccess']
            ]
        ];
    }

    public function getValidInputs()
    {
        return [
            [
                ['appTitle' => 'azerty', 'minimumAge' => 18, 'freeAccess' => 0],
                ['appTitle' => 'azerty', 'minimumAge' => 18, 'freeAccess' => false, 'subTitle' => null]
            ]
        ];
    }

}
