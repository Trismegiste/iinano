<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Utils\SkippableIterator;

use Trismegiste\SocialBundle\Utils\SkippableIterator;

/**
 * SkippableIteratorTest tests SkippableIterator
 */
class SkippableIteratorTest extends \PhpUnit_Framework_TestCase
{

    protected $sut;
    protected $wrapped;
    protected $fakeList = [111, 222, 333, 444, 555];

    public function factoryDoc()
    {
        return 111;
    }

    protected function setUp()
    {
        $this->wrapped = $this->getMockBuilder('Trismegiste\Yuurei\Persistence\CollectionIterator')
                ->disableOriginalConstructor()
                ->getMock();

        $this->sut = new SkippableIterator($this->wrapped);
    }

    public function testArray()
    {
        
    }

}