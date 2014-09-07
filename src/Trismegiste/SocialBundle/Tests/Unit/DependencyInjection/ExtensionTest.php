<?php

/*
 * iinano test
 */

namespace Trismegiste\SocialBundle\Tests\Unit\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Trismegiste\SocialBundle\DependencyInjection\Extension;

/**
 * ExtensionTest is a test for the building of services provided by this bundle
 */
class ExtensionTest extends \PHPUnit_Framework_TestCase
{

    protected $container;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        // dependencies with other packages :
        $this->container->setParameter('kernel.root_dir', '/var/tmp');
        $this->container->set('dokudoki.repository', $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface'));
        $this->container->set('security.context', $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface'));
        $this->container->set('security.encoder_factory', $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface'));

        // building extension
        $extension = new Extension();
        $fullConfig = array(
            'nickname_regex' => '[-\\da-z]+',
            'alias' => array(
                'user' => 'netizen',
                'content' => array('message')
            ),
            'pagination' => 20,
        );
        $extension->load(array($fullConfig), $this->container);
        $this->container->compile();
    }

    protected function tearDown()
    {
        unset($this->container);
    }

    public function testConfigRoot()
    {
        $extension = new Extension();
        $this->assertEquals('iinano', $extension->getAlias());
    }

    /**
     * @dataProvider getService
     */
    public function testServiceExists($name, $fqcn)
    {
        $this->assertInstanceOf($fqcn, $this->container->get($name));
    }

    public function getService()
    {
        return [
            ['social.netizen.repository', 'Trismegiste\SocialBundle\Repository\NetizenRepository'],
            ['social.content.repository', 'Trismegiste\SocialBundle\Repository\PublishingRepository'],
            ['security.netizen.provider', 'Trismegiste\SocialBundle\Security\NetizenProvider'],
            ["security.owner.voter", "Trismegiste\SocialBundle\Security\OwnerVoter"]
        ];
    }

//
//    public function testServiceCollection()
//    {
//        $this->assertInstanceOf('MongoCollection', $this->container->get('dokudoki.collection'));
//    }
//
//    public function testFacade()
//    {
//        $this->assertInstanceOf('Trismegiste\Yuurei\Facade\Provider', $this->container->get('dokudoki.facade'));
//    }
}