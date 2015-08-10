<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\DependencyInjection;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;
use Trismegiste\SocialBundle\DependencyInjection\Configuration;

/**
 * ConfigurationTest is a unit test for configuration of this bundle
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    protected function processConfig($fch)
    {
        $def = Yaml::parse(__DIR__ . '/' . $fch);
        $cfg = new Configuration();
        $proc = new Processor();

        return $proc->processConfiguration($cfg, array($def));
    }

    public function testForRequisites()
    {
        $cfg = $this->processConfig('config_minimal.yml');

        $expected = array(
            'nickname_regex' => '[-\\da-z]+',
            'pagination' => 20,
            'commentary_preview' => 3,
            'commentary_limit' => 30,
            'dynamic_default' => [],
            'quota' => [
                'storage' => ['picture' => 10e9, 'database' => 2e9],
                'bandwidth' => ['limit' => 300e9, 'name' => 'eth0']
            ]
        );
        $this->assertEquals($expected, $cfg);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage nickname_regex
     */
    public function testFailOnNonExistingContent()
    {
        $cfg = $this->processConfig('config_fail1.yml');
    }

    public function testFullConfig()
    {
        $cfg = $this->processConfig('config_full.yml');
        $expected = [
            'nickname_regex' => '[-\\da-z]+',
            'pagination' => 30,
            'commentary_preview' => 5,
            'commentary_limit' => 99,
            'dynamic_default' => [],
            'quota' => [
                'storage' => ['picture' => 33, 'database' => 22],
                'bandwidth' => ['limit' => 444, 'name' => 'eth0']
            ]
        ];
        $this->assertEquals($expected, $cfg);
    }

}
