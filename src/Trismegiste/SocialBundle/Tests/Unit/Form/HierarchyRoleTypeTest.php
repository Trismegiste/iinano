<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\HierarchyRoleType;

/**
 * HierarchyRoleTypeTest tests HierarchyRoleType
 */
class HierarchyRoleTypeTest extends FormTestCase
{

    protected $roles = ['ROLE_USER' => [], 'ROLE_ADMIN' => ['ROLE_USER', 'ROLE_PROMOTE']];

    protected function createType()
    {
        return new HierarchyRoleType($this->roles);
    }

    public function getInvalidInputs()
    {
        return [
            ['ab', null],
            [null, null]
        ];
    }

    public function getValidInputs()
    {
        return [
            ['ROLE_USER', 'ROLE_USER'],
            ['ROLE_ADMIN', 'ROLE_ADMIN'],
        ];
    }

}
