<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Validator;

use Trismegiste\SocialBundle\Validator\YoutubeUrlValidator;
use Trismegiste\SocialBundle\Validator\YoutubeUrl;

/**
 * YoutubeUrlValidatorTest tests YoutubeUrlValidator
 */
class YoutubeUrlValidatorTest extends \PHPUnit_Framework_TestCase
{

    protected $context;
    protected $validator;

    protected function setUp()
    {
        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->validator = new YoutubeUrlValidator();
        $this->validator->initialize($this->context);
    }

    public function getValidUrl()
    {
        return [
            ['https://www.youtube.com/watch?v=aaaa123456'],
            ['http://www.youtube.com/watch?v=aaaa123456'],
            ['https://youtube.com/watch?v=aaaa123456'],
            ['http://youtube.com/watch?v=aaaa123456'],
            ['https://youtu.be/aaaa123456'],
            ['http://youtu.be/aaaa123456']
        ];
    }

    /**
     * @dataProvider getValidUrl
     */
    public function testValidUrl($url)
    {
        $this->context->expects($this->never())
                ->method('addViolation');

        $this->validator->validate($url, new YoutubeUrl());
    }

    public function getInvalidUrl()
    {
        return [
            ['https://www.google.com/watch?v=aaaa123456'],
            ['http://www.youtube.com/watch?w=aaaa123456'],
            ['https://youtube.com/watch?list=a123456aaa56'],
            ['http://github.com/watch?v=aaaa123456'],
            ['https://youtu.be/?v=aaaa123456'],
            ['http://youtu.be/lol.html']
        ];
    }

    /**
     * @dataProvider getInvalidUrl
     */
    public function testInvalidUrl($url)
    {
        $this->context->expects($this->once())
                ->method('addViolation');

        $this->validator->validate($url, new YoutubeUrl());
    }

}
