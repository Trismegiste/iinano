<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Ticket;

use Trismegiste\SocialBundle\Ticket\Ticket;

/**
 * TicketTest tests the Ticket entity
 */
class TicketTest extends \PHPUnit_Framework_TestCase
{

    /** @var Ticket */
    protected $sut;

    /** @var PurchaseChoice */
    protected $choice;

    protected function setUp()
    {
        $duration = new \DateInterval("P5D"); // duration of 5 days
        $this->choice = $this->getMock('Trismegiste\SocialBundle\Ticket\PurchaseChoice');
        $this->choice->expects($this->once())
                ->method('getDuration')
                ->will($this->returnValue($duration));
        $this->sut = new Ticket($this->choice, new \DateTime());
    }

    public function testNotExpired()
    {
        $this->assertTrue($this->sut->isValid());
    }

    public function testNotExpiredFutur()
    {
        $now = new \DateTime();
        $now->modify("+1 day"); // we test for tomorrow
        $this->assertTrue($this->sut->isValid($now));
    }

    public function testExpired()
    {
        $now = new \DateTime();
        $now->modify("+1 month"); // we test in 1 month
        $this->assertFalse($this->sut->isValid($now));
    }

}
