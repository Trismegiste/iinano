<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\HierarchyRoleType;
use Trismegiste\SocialBundle\Form\NetizenFilterType;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Socialist\Author;

/**
 * NetizenFilterTypeTest tests NetizenFilterType
 */
class NetizenFilterTypeTest extends FormTestCase
{

    protected $roles = ['ROLE_USER' => [], 'ROLE_ADMIN' => ['ROLE_USER', 'ROLE_PROMOTE']];

    protected function createType()
    {
        return [
            new NetizenFilterType('[a-z]+'),
            new HierarchyRoleType($this->roles)
        ];
    }

    public function getInvalidInputs()
    {
        return [
            [['group' => 'ab', 'sort' => 'zzz', 'nickname' => '-'], ['nickname' => '-'], ['group', 'sort', 'nickname']],
            [['group' => null, 'sort' => 'zzz', 'nickname' => 'aa'], ['group' => null, 'nickname' => 'aa'], ['group', 'sort']],
            [['group' => null, 'sort' => null], ['group' => null, 'sort' => null, 'nickname' => null], ['group', 'sort']]
        ];
    }

    public function getValidInputs()
    {
        return [
            [['group' => 'ROLE_ADMIN', 'sort' => '_id -1'], ['group' => 'ROLE_ADMIN', 'sort' => '_id -1', 'nickname' => null]]
        ];
    }

}
