<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Utils\Health;

use Trismegiste\SocialBundle\Utils\Health\ServerStatus;

/**
 * ServerStatusTest tests ServerStatus
 */
class ServerStatusTest extends \PHPUnit_Framework_TestCase
{

    /** @var ServerStatus */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new ServerStatus();
    }

    public function testForCC()
    {
        $this->assertCount(3, $this->sut->getCpuLoad());
        $this->assertLessThan(1, $this->sut->getFreeSpaceRatio());
        $this->assertArrayHasKey('MemFree', $this->sut->getMemoryLoad());
    }

}
