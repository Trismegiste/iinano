<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

/**
 * CommentaryFactory is a Concrete Factory Pattern for Commentary
 */
interface CommentaryFactory
{

    /**
     * Create a new Commentary
     *
     * @return \Trismegiste\Socialist\Commentary
     */
    public function create();
}
