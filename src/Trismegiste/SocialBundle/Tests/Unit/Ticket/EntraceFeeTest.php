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

    /** @var EntranceFee */
    protected $sut;

    /** @var DateInterval */
    protected $duration;

    protected function setUp()
    {
        $this->duration = new \DateInterval("P5D"); // duration of 5 days
        $this->sut = new EntranceFee($this->duration, 100, 'EUR');
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidatorNullAmout()
    {
        new EntranceFee($this->duration, 0, 'XXX');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidatorNegativeAmout()
    {
        new EntranceFee($this->duration, -10, 'XXX');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidatorBadCurrency()
    {
        new EntranceFee($this->duration, 10, 'EU');
    }

}
