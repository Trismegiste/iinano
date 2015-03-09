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
    protected $duration;

    protected function setUp()
    {
        $this->duration = '+5 days';
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
        $this->sut->setDuration($this->duration);
        $this->assertEquals($this->duration, $this->sut->getDuration());
    }

}
