<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Utils;

use Trismegiste\Yuurei\Persistence\CollectionIterator;

/**
 * FakeCollection is a fake collection for testing
 */
class FakeCollection extends CollectionIterator
{

    protected $inner;
    protected $fakeList = [111, 222, 333, 444, 555];

    public function __construct($mock)
    {
        $this->inner = new \ArrayObject();

        foreach ($this->fakeList as $pk) {
            $doc = $mock->getMock('Trismegiste\Yuurei\Persistence\Persistable');
            $doc->expects($mock->any())
                    ->method('getId')
                    ->will($mock->returnValue($pk));
            $this->inner->append($doc);
        }

        $this->inner = $this->inner->getIterator();
    }

    public function current()
    {
        return $this->inner->current();
    }

    public function key()
    {
        return $this->inner->key();
    }

    public function next()
    {
        $this->inner->next();
    }

    public function rewind()
    {
        $this->inner->rewind();
    }

    public function valid()
    {
        return $this->inner->valid();
    }

}