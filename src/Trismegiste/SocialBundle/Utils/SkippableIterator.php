<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Utils;

use Trismegiste\Yuurei\Persistence\CollectionIterator;

/**
 * SkippableIterator is a Decorator on CollectionIterator
 * 
 * It adds the capabilities of skipping some documents whose you
 * provide the primary key (in string)
 */
class SkippableIterator extends \FilterIterator
{

    protected $skipped = [];

    /**
     * Builds the decorator on a CollectionIterator from Yuurei
     * 
     * @param \Trismegiste\Yuurei\Persistence\CollectionIterator $it
     * @param array $primaryKey a array of string of PK
     */
    public function __construct(CollectionIterator $it, array $primaryKey = [])
    {
        parent::__construct($it);
        $this->skipped = $primaryKey;
    }

    public function accept()
    {
        return !in_array(parent::current()->getId(), $this->skipped);
    }

}