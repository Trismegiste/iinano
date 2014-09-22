<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\GenderType;

/**
 * GenderTypeTest tests GenderType
 */
class GenderTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new GenderType();
    }

    public function getInvalidInputs()
    {
        return [
            ['ab', null],
        ];
    }

    public function getValidInputs()
    {
        return [
            ['xx', 'xx'],
            ['xy', 'xy'],
        ];
    }

}
