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
        $this->sut->setGroup('ROLE_ADMIN');
        $this->assertEquals('ROLE_ADMIN', $this->sut->getGroup());
        $this->assertEquals(['ROLE_ADMIN'], $this->sut->getRoles()); // for BC
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

        $this->sut->setCredential($strat);
        $this->assertEquals($strat, $this->sut->getCredential());
        $this->sut->eraseCredentials(); // for CC
    }

    public function testNoTicketAtCreation()
    {
        $this->assertNull($this->sut->getLastTicket());
    }

    public function testNoValidTicketAtCreation()
    {
        $this->assertFalse($this->sut->hasValidTicket());
    }

    /**
     * @expectedException \Trismegiste\SocialBundle\Ticket\InvalidTicketException
     * @expectedExceptionMessage ticket is not valid
     */
    public function testAddInvalidTicket()
    {
        $ticket = $this->getMock('Trismegiste\SocialBundle\Ticket\EntranceAccess');
        $ticket->expects($this->once())
                ->method('isValid')
                ->willReturn(false);

        $this->sut->addTicket($ticket);
    }

    public function testAddValidTicket()
    {
        $ticket = $this->getMock('Trismegiste\SocialBundle\Ticket\EntranceAccess');
        $ticket->expects($this->exactly(2))
                ->method('isValid')
                ->willReturn(true);

        $this->sut->addTicket($ticket);

        $this->assertTrue($this->sut->hasValidTicket());
        $this->assertEquals($ticket, $this->sut->getLastTicket());
        $this->assertCount(1, $this->sut->getTicketIterator());
    }

    /**
     * @expectedException \Trismegiste\SocialBundle\Ticket\InvalidTicketException
     * @expectedExceptionMessage currently a valid ticket
     */
    public function testNoAddingTicketOnAlreadyValid()
    {
        $ticket = $this->getMock('Trismegiste\SocialBundle\Ticket\EntranceAccess');
        $ticket->expects($this->exactly(3))
                ->method('isValid')
                ->willReturn(true);

        $this->sut->addTicket($ticket);
        $this->assertTrue($this->sut->hasValidTicket());

        $ticket = $this->getMock('Trismegiste\SocialBundle\Ticket\EntranceAccess');
        $ticket->expects($this->once())
                ->method('isValid')
                ->willReturn(true);
        $this->sut->addTicket($ticket);
    }

    public function testUnused()
    {
        $this->sut->getSalt();
        $this->sut->getPassword();
    }

    public function getComparableUser()
    {
        $user = new Netizen(new \Trismegiste\Socialist\Author('kirk'));
        $user->setGroup('USER');
        $user2 = clone $user;
        $user->setGroup('ADMIN');
        $user3 = clone $user;

        return [
            [$user, $user, true],
            [$user, $user2, false],
            [$user, $user3, true]
        ];
    }

    /**
     * @dataProvider getComparableUser
     */
    public function testIsEqual(Netizen $user1, Netizen $user2, $yesOrNo)
    {
        $this->assertEquals($yesOrNo, $user1->isEqualTo($user2));
    }

}
