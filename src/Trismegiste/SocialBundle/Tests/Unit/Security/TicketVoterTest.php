<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security;

use Trismegiste\SocialBundle\Security\TicketVoter;
use Trismegiste\Socialist\Author;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * TicketVoterTest tests the voter TicketVoter
 */
class TicketVoterTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;
    protected $currentUser;
    protected $token;

    protected function createDocument($nick)
    {
        return $this->getMockForAbstractClass('Trismegiste\Socialist\Content', [new Author($nick)]);
    }

    protected function setUp()
    {
        $this->sut = new TicketVoter();

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

    public function testValidTicketGranted()
    {
        $this->currentUser->expects($this->any())
                ->method('hasValidTicket')
                ->willReturn(true);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $this->sut->vote($this->token, null, ['VALID_TICKET']));
    }

    public function testNoValidTicketDenied()
    {
        $this->currentUser->expects($this->any())
                ->method('hasValidTicket')
                ->willReturn(false);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->sut->vote($this->token, null, ['VALID_TICKET']));
    }

    public function testDeniedUnauthenticated()
    {
        $anonymous = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->sut->vote($anonymous, null, ['VALID_TICKET']));
    }

    public function testAbstainOnUnsupportedAttribute()
    {
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->sut->vote($this->token, null, ['WESH']));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOneAtribute()
    {
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->sut->vote($this->token, null, ['FLIP', 'FLOP']));
    }

}
