<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security;

use Trismegiste\SocialBundle\Security\ListenerVoter;
use Trismegiste\Socialist\Author;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;

/**
 * ListenerVoterTest tests ListenerVoter
 */
class ListenerVoterTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;
    protected $currentUser;
    protected $token;
    protected $manager;

    protected function setUp()
    {
        $this->sut = new ListenerVoter();

        $this->currentUser = $this->getMockBuilder('Trismegiste\SocialBundle\Security\Netizen')
                ->disableOriginalConstructor()
                ->getMock();
        $this->currentUser->expects($this->any())
                ->method('getAuthor')
                ->will($this->returnValue(new Author('kirk')));

        $this->token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->token->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue($this->currentUser));

        $this->manager = new AccessDecisionManager([$this->sut]);
    }

    public function testBadClass()
    {
        $this->assertFalse($this->manager->decide($this->token, ['LISTENER'], new \StdClass()));
    }

    public function testBadAttribute()
    {
        $target = $this->getMock('Trismegiste\Socialist\AuthorInterface');
        $this->assertFalse($this->manager->decide($this->token, ['YOPYOP'], $target));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMultipleAtrributes()
    {
        $target = $this->getMock('Trismegiste\Socialist\AuthorInterface');
        $this->assertFalse($this->manager->decide($this->token, ['YOPYOP', 'YAPYAP'], $target));
    }

    public function testBadLoggedUser()
    {
        $anonymous = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $target = $this->getMock('Trismegiste\Socialist\AuthorInterface');
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->sut->vote($anonymous, $target, ['LISTENER']));
    }

    public function testNoFollower()
    {
        $target = $this->getMock('Trismegiste\Socialist\AuthorInterface');
        $target->expects($this->once())
                ->method('getNickname')
                ->will($this->returnValue('dummy'));

        $this->currentUser->expects($this->once())
                ->method('followerExists')
                ->with($this->equalTo('dummy'))
                ->will($this->returnValue(false));

        $this->assertFalse($this->manager->decide($this->token, ['LISTENER'], $target));
    }

    public function testGranted()
    {
        $target = $this->getMock('Trismegiste\Socialist\AuthorInterface');
        $target->expects($this->once())
                ->method('getNickname')
                ->will($this->returnValue('spock'));

        $this->currentUser->expects($this->once())
                ->method('followerExists')
                ->with($this->equalTo('spock'))
                ->will($this->returnValue(true));

        $this->assertTrue($this->manager->decide($this->token, ['LISTENER'], $target));
    }

}
