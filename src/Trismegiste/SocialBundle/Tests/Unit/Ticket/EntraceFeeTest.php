<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Ticket;

use Trismegiste\SocialBundle\Ticket\EntranceFee;

/**
 * EntraceFeeTest tests the EntraceFee model
 */
class EntraceFeeTest extends \PHPUnit_Framework_TestCase
{

    /** @var Ticket */
    protected $sut;

    /** @var EntranceFee */
    protected $choice;

    protected function setUp()
    {
        $duration = new \DateInterval("P5D"); // duration of 5 days
        $this->sut = new EntranceFee($duration, 100, 'EUR');
    }

    public function testAmoutGetter()
    {
        $this->assertEquals(100, $this->sut->getAmount());
    }

    public function testCurrencyGetter()
    {
        $this->assertEquals('EUR', $this->sut->getCurrency());
    }

    public function testDurationGetter()
    {
        $this->assertInstanceOf('DateInterval', $this->sut->getDuration());
    }

}
