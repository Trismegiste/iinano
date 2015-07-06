<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\VideoDataTransformer;
use Trismegiste\Socialist\Author;
use Trismegiste\Socialist\Video;

/**
 * VideoDataTransformerTest tests the youtube's url transformer to embedded url
 */
class VideoDataTransformerTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $this->sut = new VideoDataTransformer();
    }

    public function getVideoUrl()
    {
        $result = 'http://www.youtube.com/embed/aaaa123456';
        return [
            ['http://youtu.be/aaaa123456', $result],
            ['https://youtube.com/watch?v=aaaa123456', $result],
            ['http://www.youtube.com/watch?v=aaaa123456', $result],
        ];
    }

    /**
     * @dataProvider getVideoUrl
     */
    public function testReverseTransform($original, $embed)
    {
        $obj = new Video(new Author('kirk'));
        $obj->setUrl($original);
        $this->assertEquals($embed, $this->sut->reverseTransform($obj)->getUrl());
    }

    public function testIdentity()
    {
        $this->assertEquals('who cares', $this->sut->transform('who cares'));
    }

}
