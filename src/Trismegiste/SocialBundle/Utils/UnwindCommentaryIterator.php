<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Utils;

/**
 * UnwindCommentaryIterator is a Decorator for aggregation of commentary
 *
 * Main goal : provide a composite primary key with _id from Publishing and Uuid from the Commentary
 *
 * @see self::key()
 */
class UnwindCommentaryIterator implements \Iterator
{

    protected $inner;

    public function __construct(\Iterator $it)
    {
        $this->inner = $it;
    }

    public function current()
    {
        return $this->inner->current();
    }

    public function key()
    {
        return $this->inner->current()['_id'] . '-' . $this->inner->current()['commentary']['uuid'];
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
