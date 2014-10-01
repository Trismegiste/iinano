<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Utils;

use Trismegiste\SocialBundle\Utils\KeyRegexFilter;

/**
 * KeyRegexFilterTest tests KeyRegexFilter
 */
class KeyRegexFilterTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $iterator = new \ArrayIterator(['megatherion' => true, 'stratovarius' => true]);
        $this->sut = new KeyRegexFilter($iterator, '#therion#');
    }

    public function testAccept()
    {
        $this->assertCount(1, $this->sut);
    }

}
