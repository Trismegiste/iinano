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

    protected function setUp()
    {
        $this->sut = new EntranceFee();
    }

    public function testAmoutGetter()
    {
        $this->sut->setAmount(9.99);
        $this->assertEquals(9.99, $this->sut->getAmount());
    }

    public function testCurrencyGetter()
    {
        $this->sut->setCurrency('EUR');
        $this->assertEquals('EUR', $this->sut->getCurrency());
    }

    public function testDurationGetter()
    {
        $this->sut->setDurationValue(12);
        $this->assertEquals(12, $this->sut->getDurationValue());
        $this->assertEquals('+12 month', $this->sut->getDurationOffset());
    }

    public function testTitle()
    {
        $this->assertStringStartsWith('payment', $this->sut->getTitle());
    }

}
