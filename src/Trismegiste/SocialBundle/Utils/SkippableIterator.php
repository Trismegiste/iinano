<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle;

use Trismegiste\Yuurei\Persistence\CollectionIterator;

/**
 * SkippableIterator is a Decorator on CollectionIterator
 * 
 * It adds the capabilities of skipping some documents whose you
 * provide the primary key (in string)
 */
class SkippableIterator implements \Iterator
{

    protected $wrapped;
    protected $skipped = [];

    /**
     * Builds the decorator on a CollectionIterator from Yuurei
     * 
     * @param \Trismegiste\Yuurei\Persistence\CollectionIterator $it
     * @param array $primaryKey a array of string of PK
     */
    public function __construct(CollectionIterator $it, array $primaryKey = [])
    {
        $this->wrapped = $it;
        $this->skipped = $primaryKey;
    }

    /**
     * The core of this class :
     */
    protected function skipToNext()
    {
        while ($this->wrapped->valid() &&
        // @todo Demeter's law violation :
        in_array((string) $this->wrapped->current()->getId(), $this->skipped)) {
            $this->wrapped->next();
        }
    }

    // next methods are classical

    public function current()
    {
        return $this->wrapped->current();
    }

    public function key()
    {
        return $this->wrapped->key();
    }

    public function next()
    {
        $this->wrapped->next();
        $this->skipToNext();
    }

    public function rewind()
    {
        $this->wrapped->rewind();
        $this->skipToNext();
    }

    public function valid()
    {
        return $this->wrapped->valid();
    }

}