<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Utils;

use Trismegiste\SocialBundle\Utils\UnwindCommentaryIterator;

/**
 * UnwindCommentaryIteratorTest tests UnwindCommentaryIterator
 */
class UnwindCommentaryIteratorTest extends \PhpUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $this->sut = new UnwindCommentaryIterator(new \ArrayIterator([0 => [
                '_id' => 123,
                'commentary' => ['uuid' => 456]
        ]]));
    }

    public function testAll()
    {
        $res = iterator_to_array($this->sut);
        $this->assertCount(1, $res);
        $this->assertArrayHasKey("123-456", $res);
    }

}
