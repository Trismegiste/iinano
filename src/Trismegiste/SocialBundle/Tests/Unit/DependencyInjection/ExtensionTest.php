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
        // directory structure
        foreach (['/app', '/cache', '/storage/picture', '/storage/cache'] as $dir) {
            $tmp = sys_get_temp_dir() . $dir;
            if (!file_exists($tmp)) {
                mkdir($tmp, 0777, true);
            }
        }

        $this->container = new ContainerBuilder();
        // dependencies with other packages :
        $this->container->setParameter('kernel.root_dir', sys_get_temp_dir() . '/app');
        $this->container->setParameter('kernel.cache_dir', sys_get_temp_dir() . '/cache');
        $this->container->setParameter('security.role_hierarchy.roles', []);
        $this->container->set('dokudoki.repository', $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface'));
        $this->container->set('security.context', $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface'));
        $this->container->set('logger', $this->getMock('Psr\Log\LoggerInterface'));
        $this->container->set('form.factory', $this->getMock('Symfony\Component\Form\FormFactoryInterface'));
        $this->container->set('security.http_utils', $this->getMockBuilder('Symfony\Component\Security\Http\HttpUtils')
                        ->disableOriginalConstructor()
                        ->getMock());
        $this->container->set('dokudoki.collection', $this->getMockBuilder('MongoCollection')
                        ->disableOriginalConstructor()
                        ->getMock());

        // building extension
        $extension = new Extension();
        $minConfig = array(
            'nickname_regex' => '[-\\da-z]+'
        );
        $extension->load(array($minConfig), $this->container);
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
            ['security.netizen.provider', 'Trismegiste\SocialBundle\Security\NetizenProvider'],
            ["security.owner.voter", "Trismegiste\SocialBundle\Security\OwnerVoter"],
            ['social.publishing.repository', 'Trismegiste\SocialBundle\Repository\PublishingRepository']
        ];
    }

    public function getParameter()
    {
        return [
            ['social.commentary_preview', 3],
            ['social.pagination', 20]
        ];
    }

    /**
     * @dataProvider getParameter
     */
    public function testParameterExists($paramName, $expectedValue)
    {
        $this->assertEquals($expectedValue, $this->container->getParameter($paramName));
    }

}
