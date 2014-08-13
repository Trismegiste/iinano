<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

use Trismegiste\SocialBundle\Model\Vertex;

/**
 * VertexTest tests the vertex
 */
class VertexTest extends \PHPUnit_Framework_TestCase
{

    protected $stopwatch;
    protected $sut;

    protected function setUp()
    {
        $this->stopwatch = new \DateTime();
        $this->sut = new Vertex();
    }

    public function testCreationDate()
    {
        $this->assertGreaterThanOrEqual($this->stopwatch, $this->sut->getLastEdit());
    }

    public function testContent()
    {
        $this->sut->setContent("Kirk");
        $this->assertNotEquals('Spock', $this->sut->getContent());
        $this->assertEquals('Kirk', $this->sut->getContent());
    }

    public function testChangeTimestamp()
    {
        $ref = new \DateTime("yesterday 14:00");
        $this->sut->setLastEdit($ref);
        $this->assertLessThan($this->stopwatch, $this->sut->getLastEdit());
        $this->assertEquals($ref, $this->sut->getLastEdit());
    }

}