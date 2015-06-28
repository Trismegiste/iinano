<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Utils;

use Trismegiste\SocialBundle\Utils\PerformanceBenchmark;

/**
 * PerformanceBenchmarkTest tests PerformanceBenchmark service
 */
class PerformanceBenchmarkTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $this->sut = new PerformanceBenchmark();
    }

    public function testStopwatch()
    {
        $this->assertRegExp('#\d+ ms#', $this->sut->getTimeDelay());
    }

    public function testMemory()
    {
        $this->assertGreaterThan(0, $this->sut->getMemoryUsage());
    }

}
