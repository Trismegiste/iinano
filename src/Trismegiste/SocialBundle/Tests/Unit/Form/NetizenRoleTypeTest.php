<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\HierarchyRoleType;
use Trismegiste\SocialBundle\Form\NetizenRoleType;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Socialist\Author;

/**
 * NetizenRoleTypeTest tests NetizenRoleType
 */
class NetizenRoleTypeTest extends FormTestCase
{

    protected $roles = ['ROLE_USER' => [], 'ROLE_ADMIN' => ['ROLE_USER', 'ROLE_PROMOTE']];

    protected function createType()
    {
        return [
            new NetizenRoleType(),
            new HierarchyRoleType($this->roles)
        ];
    }

    protected function createData()
    {
        return new Netizen(new Author('kirk'));
    }

    public function getInvalidInputs()
    {
        $user = new Netizen(new Author('kirk'));
        return [
            [['group' => 'ab'], $user],
            [['group' => null], $user]
        ];
    }

    public function getValidInputs()
    {
        $promoted = new Netizen(new Author('kirk'));
        $promoted->setGroup('ROLE_ADMIN');
        return [
            [['group' => 'ROLE_ADMIN'], $promoted]
        ];
    }

}
