<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\InstallParamType;

/**
 * InstallParamTypeTest tests InstallParamType
 */
class InstallParamTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new InstallParamType();
    }

    public function getInvalidInputs()
    {
        return [
            [[], ['facebook' => null, 'twitter' => null]],
            [
                ['facebook' => ['client_id' => 123]],
                ['facebook' => ['client_id' => 123, 'secret_id' => null], 'twitter' => null],
                ['facebook']
            ],
            [
                ['facebook' => ['secret_id' => 123]],
                ['facebook' => ['client_id' => null, 'secret_id' => 123], 'twitter' => null],
                ['facebook']
            ],
            [
                ['facebook' => ['secret_id' => 123], 'twitter' => ['client_id' => 456]],
                ['facebook' => ['client_id' => null, 'secret_id' => 123], 'twitter' => ['client_id' => 456, 'secret_id' => null]],
                ['facebook', 'twitter']
            ],
        ];
    }

    public function getValidInputs()
    {
        return [
            [
                ['facebook' => ['client_id' => 123, 'secret_id' => 456]],
                ['facebook' => ['client_id' => 123, 'secret_id' => 456], 'twitter' => null],
            ],
            [
                ['twitter' => ['client_id' => 123, 'secret_id' => 456]],
                ['twitter' => ['client_id' => 123, 'secret_id' => 456], 'facebook' => null],
            ],
            [
                ['facebook' => ['client_id' => 123, 'secret_id' => 456], 'twitter' => ['client_id' => 789, 'secret_id' => '0AB']],
                ['facebook' => ['client_id' => 123, 'secret_id' => 456], 'twitter' => ['client_id' => 789, 'secret_id' => '0AB']],
            ]
        ];
    }

}
