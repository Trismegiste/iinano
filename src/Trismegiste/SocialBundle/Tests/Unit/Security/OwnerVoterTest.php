<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security;

use Trismegiste\SocialBundle\Security\OwnerVoter;
use Trismegiste\Socialist\SimplePost;
use Trismegiste\Socialist\Author;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * OwnerVoterTest tests the voter OwnerVoter
 */
class OwnerVoterTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;
    protected $currentUser;
    protected $token;

    protected function setUp()
    {
        $this->sut = new OwnerVoter();

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
    }

    public function testGranted()
    {
        $doc = new SimplePost(new Author('kirk'));
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $this->sut->vote($this->token, $doc, ['OWNER']));
    }

    public function testDeniedOwning()
    {
        $doc = new SimplePost(new Author('spock'));
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->sut->vote($this->token, $doc, ['OWNER']));
    }

    public function testDeniedUnauthenticated()
    {
        $doc = new SimplePost(new Author('kirk'));
        $anonymous = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->sut->vote($anonymous, $doc, ['OWNER']));
    }

    public function testAbstainNonContent()
    {
        $doc = new \stdClass();
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->sut->vote($this->token, $doc, ['OWNER']));
    }

    public function testAbstainNonOwner()
    {
        $doc = new SimplePost(new Author('kirk'));
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->sut->vote($this->token, $doc, ['WESH']));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOneAtribute()
    {
        $doc = $this->getMockBuilder('Trismegiste\Socialist\Content')
                ->disableOriginalConstructor()
                ->getMock();
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->sut->vote($this->token, $doc, ['FLIP', 'FLOP']));
    }

}