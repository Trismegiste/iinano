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
            'alias' => array(
                'user' => 'netizen',
                'content' => array('message')
            ),
            'pagination' => 20,
        );
        $this->assertEquals($expected, $cfg);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage trismegiste_social.alias.content
     */
    public function testFailOnNonExistingClass()
    {
        $cfg = $this->processConfig('config_fail.yml');
    }

    public function testFullConfig()
    {
        $cfg = $this->processConfig('config_full.yml');
        $expected = array(
            'nickname_regex' => '[-\\da-z]+',
            'alias' =>
            array(
                'user' => 'netizen',
                'content' =>
                array(
                    0 => 'message',
                    1 => 'tweet',
                    2 => 'pull-request',
                ),
            ),
            'pagination' => 30,
        );
        $this->assertEquals($expected, $cfg);
    }

}

