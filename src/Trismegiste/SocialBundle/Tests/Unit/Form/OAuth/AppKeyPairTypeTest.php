<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form\Oauth;

use Trismegiste\SocialBundle\Form\Oauth\AppKeyPairType;
use Trismegiste\SocialBundle\Tests\Unit\Form\FormTestCase;

/**
 * AppKeyPairTypeTest tests the AppKeyPairType form
 */
class AppKeyPairTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new AppKeyPairType();
    }

    public function getInvalidInputs()
    {
        return [
            [['client_id' => 123, 'secret_id' => ''], ['client_id' => 123, 'secret_id' => '']],
            [['client_id' => null, 'secret_id' => '123'], ['client_id' => null, 'secret_id' => '123']]
        ];
    }

    public function getValidInputs()
    {
        return [
            [['client_id' => 123, 'secret_id' => '456'], ['client_id' => 123, 'secret_id' => '456']],
            [['client_id' => '', 'secret_id' => ''], null]
        ];
    }

}
