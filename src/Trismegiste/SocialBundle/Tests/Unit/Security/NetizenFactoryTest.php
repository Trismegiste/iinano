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
    protected $encoder;

    protected function setUp()
    {
        $this->encoder = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $this->sut = new NetizenFactory($this->encoder);
    }

    public function testUserCreation()
    {
        $this->encoder->expects($this->once())
                ->method('getEncoder')
                ->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface')));

        $user = $this->sut->create('kirk', 'ncc1701');

        $this->assertInstanceOf('Trismegiste\Socialist\Author', $user->getAuthor());
        $this->assertInstanceOf('Trismegiste\SocialBundle\Security\Profile', $user->getProfile());
        $this->assertTrue(class_exists($user->getCredentialType()));
    }

}
