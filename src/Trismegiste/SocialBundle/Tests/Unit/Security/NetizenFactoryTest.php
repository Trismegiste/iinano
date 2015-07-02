<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Security\NetizenFactory;
use Trismegiste\SocialBundle\Security\Netizen;

/**
 * NetizenFactoryTest tests NetizenFactory
 */
class NetizenFactoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var NetizenFactory */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new NetizenFactory($this->encoder);
    }

    public function testUserCreation()
    {
        $user = $this->sut->create('kirk', 'dummy', '123456789');

        $this->assertInstanceOf('Trismegiste\Socialist\Author', $user->getAuthor());
        $this->assertInstanceOf('Trismegiste\SocialBundle\Security\Profile', $user->getProfile());
        $this->assertEquals('dummy', $user->getCredential()->getProviderKey());
        $this->assertEquals('123456789', $user->getCredential()->getUid());
    }

}
