<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\DependencyInjection;

use Trismegiste\SocialBundle\DependencyInjection\ImportAliases;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * ImportAliasesTest tests ImportAliases
 */
class ImportAliasesTest extends \PHPUnit_Framework_TestCase
{

    /** @var Trismegiste\SocialBundle\DependencyInjection\ImportAliases */
    protected $sut;

    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder */
    protected $containerBuilder;

    protected function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder->setDefinitions([
            'social.content.repository' => new Definition(null, [0, 1, 2]),
            'twig.social.renderer' => new Definition(null, [0, 1]),
            'social.publishing.repository' => new Definition(null, [0, 1, 2]),
            'social.netizen.repository' => new Definition(null, [0, 1, 2]),
            'social.private_message.repository' => new Definition(null, [0, 1, 2]),
            'social.abusereport.repository' => new Definition(null, [0, 1, 2])
        ]);
        $this->sut = new ImportAliases();
    }

    public function testImporting()
    {
        $aliasMap = [
            'sample' => 'Trismegiste\Socialist\Status',
            'netizen' => 'Trismegiste\SocialBundle\Security\Netizen',
            'private' => 'Trismegiste\Socialist\PrivateMessage'
        ];
        $this->containerBuilder->setDefinition(
                'dokudoki.builder.whitemagic', new Definition(null, [$aliasMap])
        );

        $this->sut->process($this->containerBuilder);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Publishing
     */
    public function testMissingContent()
    {
        $aliasMap = [
            'sample' => 'stdClass', // note this config should be rejected by dokudoki (not Persistable)
            'netizen' => 'Trismegiste\SocialBundle\Security\Netizen',
            'private' => 'Trismegiste\Socialist\PrivateMessage'
        ];
        $this->containerBuilder->setDefinition(
                'dokudoki.builder.whitemagic', new Definition(null, [$aliasMap])
        );

        $this->sut->process($this->containerBuilder);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage User
     */
    public function testMissingUser()
    {
        $aliasMap = [
            'sample' => 'Trismegiste\Socialist\Status',
            'netizen' => 'Trismegiste\Socialist\User',
            'netizen2' => 'Trismegiste\Socialist\User',
            'private' => 'Trismegiste\Socialist\PrivateMessage'
        ];
        $this->containerBuilder->setDefinition(
                'dokudoki.builder.whitemagic', new Definition(null, [$aliasMap])
        );

        $this->sut->process($this->containerBuilder);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage PrivateMessage
     */
    public function testPm()
    {
        $aliasMap = [
            'sample' => 'Trismegiste\Socialist\Status',
            'netizen' => 'Trismegiste\SocialBundle\Security\Netizen'
        ];
        $this->containerBuilder->setDefinition(
                'dokudoki.builder.whitemagic', new Definition(null, [$aliasMap])
        );

        $this->sut->process($this->containerBuilder);
    }

}
