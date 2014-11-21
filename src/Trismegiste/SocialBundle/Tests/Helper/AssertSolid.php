<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Helper;

/**
 * AssertSolid is an implementation of assertions for SOLID compliance
 */
trait AssertSolid
{

    protected function assertMethodCountEquals($inheritFqcn, array $motherFqcn, $delta = 0)
    {
        $methodCount = $delta;
        foreach ($motherFqcn as $fqcn) {
            $methodCount += count(get_class_methods($fqcn));
        }

        $this->assertEquals(count(get_class_methods($inheritFqcn)), $methodCount);
    }

}
