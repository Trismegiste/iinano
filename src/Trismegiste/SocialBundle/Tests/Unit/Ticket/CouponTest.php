<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Ticket;

use Trismegiste\SocialBundle\Ticket\Coupon;

/**
 * CouponTest tests Coupon entity
 */
class CouponTest extends \PHPUnit_Framework_TestCase
{

    /** @var Coupon */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new Coupon();
        $this->sut->expiredAt = new \DateTime('tomorrow');
        $this->sut->hashKey = 'toto';
        $this->sut->setDurationValue(5);
    }

    public function testCounter()
    {
        $this->assertEquals(0, $this->sut->getUsedCounter());
        $this->assertEquals(1, $this->sut->maximumUse);
        $this->sut->incUse();
        $this->assertEquals(1, $this->sut->getUsedCounter());
    }

    public function testValidityWithCounter()
    {
        $this->assertTrue($this->sut->isValid());
        $this->sut->incUse();
        $this->assertFalse($this->sut->isValid());
    }

    public function testValidityWithExpiration()
    {
        $this->assertTrue($this->sut->isValid());
        $this->sut->expiredAt = new \DateTime('yesterday');
        $this->assertFalse($this->sut->isValid());
    }

    public function testGetter()
    {
        $this->assertEquals('toto', $this->sut->getHashKey());
        $this->assertEquals(5, $this->sut->getDurationValue());
        $this->assertEquals('+5 day', $this->sut->getDurationOffset());
    }

}
