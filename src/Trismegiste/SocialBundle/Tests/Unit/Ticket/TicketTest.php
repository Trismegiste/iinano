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
        $this->choice = $this->getMock('Trismegiste\SocialBundle\Ticket\PurchaseChoice');
        $this->choice->expects($this->any())
                ->method('getDuration')
                ->will($this->returnValue('+5 days'));
        $this->sut = new Ticket($this->choice);
    }

    public function testPurchaseDate()
    {
        $this->assertInstanceOf('DateTime', $this->sut->getPurchasedAt());
    }

    public function testWithPurchaseDateConstruct()
    {
        $past = new \DateTime();
        $past->modify('-3 days');
        $t = new Ticket($this->choice, $past);
        $this->assertTrue($t->isValid());
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