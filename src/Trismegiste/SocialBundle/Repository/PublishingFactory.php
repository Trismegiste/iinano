<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

/**
 * PublishingFactory is a Factory Method Pattern for Publishing subclasses
 */
interface PublishingFactory
{

    /**
     * Creates a new instance of a concrete Publishing subclass
     *
     * @param string $alias one of the alias for a Publishing subclass
     *
     * @return \Trismegiste\Socialist\Publishing
     */
    public function create($alias);
}
