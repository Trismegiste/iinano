<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit;

use Trismegiste\SocialBundle\TrismegisteSocialBundle;

/**
 * TrismegisteSocialBundleTest tests TrismegisteSocialBundle
 */
class TrismegisteSocialBundleTest extends \PHPUnit_Framework_TestCase
{

    /** @var TrismegisteSocialBundle */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new TrismegisteSocialBundle();
    }

    public function testBoot()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->any())
                ->method('getParameter')
                ->will($this->returnValue(true));
        $this->sut->boot();
        $this->sut->setContainer($container);
        $this->sut->getContainerExtension();
        $this->sut->registerCommands($this->getMock('Symfony\Component\Console\Application'));
    }

    public function testBuild()
    {
        $builder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
                ->disableOriginalConstructor()
                ->getMock();

        $this->sut->build($builder);
    }

}
