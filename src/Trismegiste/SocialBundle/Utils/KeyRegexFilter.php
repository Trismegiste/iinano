<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Utils;

/**
 * KeyRegexFilter is a filter on iterator which filters
 * entries based on regex tested on each key
 */
class KeyRegexFilter extends \FilterIterator
{

    protected $regex;

    /**
     * Builds the filter
     */
    public function __construct(\Iterator $it, $regex)
    {
        parent::__construct($it);
        $this->regex = $regex;
    }

    public function accept()
    {
        return preg_match($this->regex, parent::key());
    }

}
