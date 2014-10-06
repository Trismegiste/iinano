<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security;

use Trismegiste\SocialBundle\Security\Netizen;

/**
 * NetizenTest tests Netizen
 */
class NetizenTest extends \PHPUnit_Framework_TestCase
{

    /** @var Netizen */
    protected $sut;

    /** @var Trismegiste\Socialist\AuthorInterface */
    protected $author;

    protected function setUp()
    {
        $this->author = $this->getMock('Trismegiste\Socialist\AuthorInterface');
        $this->sut = new Netizen($this->author);
    }

    public function testUsernameAuthor()
    {
        $this->author->expects($this->once())
                ->method('getNickname')
                ->will($this->returnValue('kirk'));

        $this->assertEquals('kirk', $this->sut->getUsername());
    }

    public function testRoles()
    {
        $this->sut->addRole('ROLE_USER');
        $this->assertEquals(['ROLE_USER'], $this->sut->getRoles());
    }

    public function testProfile()
    {
        $profile = $this->getMock('Trismegiste\SocialBundle\Security\Profile');
        $this->sut->setProfile($profile);
        $this->assertEquals($profile, $this->sut->getProfile());
    }

    public function testCredentialStrategy()
    {
        $strat = $this->getMock('Trismegiste\SocialBundle\Security\Credential\Strategy');
        $strat->expects($this->once())
                ->method('getPassword');
        $strat->expects($this->once())
                ->method('getSalt');

        $this->sut->setCredential($strat);
        $this->sut->getPassword();
        $this->sut->getSalt();

        $this->assertEquals(get_class($strat), $this->sut->getCredentialType());
        $this->sut->eraseCredentials(); // for CC
    }

}
