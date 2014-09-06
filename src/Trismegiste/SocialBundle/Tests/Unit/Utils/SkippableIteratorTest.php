<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Utils;

use Trismegiste\SocialBundle\Utils\SkippableIterator;

/**
 * SkippableIteratorTest tests SkippableIterator
 */
class SkippableIteratorTest extends \PhpUnit_Framework_TestCase
{

    protected $sut;
    protected $wrapped;

    protected function setUp()
    {
        $this->wrapped = new FakeCollection($this);
        $this->sut = new SkippableIterator($this->wrapped, [111, 333, 555]);
    }

    public function testArray()
    {
        $tab = iterator_to_array($this->sut);
        $this->assertEquals(222, $tab[1]->getId());
        $this->assertEquals(444, $tab[3]->getId());
    }

}