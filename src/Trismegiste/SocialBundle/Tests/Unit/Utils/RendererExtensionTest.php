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
        $this->twig->addExtension(new RendererExtension('%s.twig', ['sample' => 'PublishMock']));
        $this->twig->addExtension(new \Twig_Extensions_Extension_I18n());
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

    public function getNawak()
    {
        return [
            [null, ''],
            [123, 123],
            ['1999-06-06', '1999-06-06']
        ];
    }

    /**
     * @dataProvider getNawak
     */
    public function testBadDateTime($nawak, $expected)
    {
        $this->assertEquals($expected
                , $this->twig->render("{{ created|timeago }}", ['created' => $nawak]));
    }

    public function testChooseTemplate()
    {
        $mock = $this->getMockBuilder('Trismegiste\Socialist\Publishing')
                ->disableOriginalConstructor()
                ->setMockClassName('PublishMock')
                ->getMock();

        $result = $this->twig->render("{{ choose_template(obj) }}", ['obj' => $mock]);
        $this->assertEquals('sample.twig', $result);
    }

    public function getGender()
    {
        return [
            ['xx', 'Female'],
            ['xy', 'Male'],
            ['wz', '?']
        ];
    }

    /**
     * @dataProvider getGender
     */
    public function testGenderRender($type, $render)
    {
        $result = $this->twig->render("{{ '$type'|gender }}");
        $this->assertEquals($render, $result);
    }

    public function getConversion()
    {
        return [
            [0, '0'],
            [1, '1'],
            [42, '42'],
            [534, '534'],
            [1337, '1.34k'],
            [12345, '12.3k'],
            [123456, '123k'],
            [1234567, '1.23M']
        ];
    }

    /**
     * @dataProvider getConversion
     */
    public function testInternationalSystem($value, $expect)
    {
        $result = $this->twig->render("{{ $value|si }}");
        $this->assertEquals($expect, $result);
    }

}
