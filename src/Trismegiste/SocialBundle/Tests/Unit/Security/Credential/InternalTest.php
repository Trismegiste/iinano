<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security\Credential;

use Trismegiste\SocialBundle\Security\Credential\Internal;

/**
 * InternalTest tests Internal
 */
class InternalTest extends \PHPUnit_Framework_TestCase
{

    /** @var Internal */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new Internal('abcd', 123);
    }

    public function testInitialize()
    {
        $this->assertEquals('abcd', $this->sut->getPassword());
        $this->assertEquals(123, $this->sut->getSalt());
    }

}