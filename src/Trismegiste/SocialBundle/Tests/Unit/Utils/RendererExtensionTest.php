<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Utils;

use Trismegiste\SocialBundle\Utils\RendererExtension;

/**
 * RendererExtensionTest tests RendererExtension
 */
class RendererExtensionTest extends \PHPUnit_Framework_TestCase
{

    protected $twig;

    protected function setUp()
    {
        $loader = new \Twig_Loader_String();
        $this->twig = new \Twig_Environment($loader);
        $this->twig->addExtension(new RendererExtension());
    }

    public function getPeriod()
    {
        return [
            ['-1 day', '1 day ago'],
            ['-4 day', '4 days ago'],
            ['-7 day', '1 week ago'],
            ['-21 day', '3 weeks ago'],
            ['-31 day', '1 month ago'],
            ['-62 day', '2 months ago'],
            ['-365 day', '1 year ago'],
            ['-800 day', '2 years ago'],
            ['+1 day', 'in 1 day'],
            ['+1800 second', 'in 30 minutes'],
            ['now', 'now']
        ];
    }

    /**
     * @dataProvider getPeriod
     */
    public function testRender($delta, $expected)
    {
        $thisTime = new \DateTime($delta);

        $this->assertStringStartsWith("It's $expected"
                , $this->twig->render("It's {{ created|timeago }}"
                        , array('created' => $thisTime)));
    }

}