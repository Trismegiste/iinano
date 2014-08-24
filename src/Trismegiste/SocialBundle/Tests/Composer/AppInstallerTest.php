<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Composer;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trismegiste\SocialBundle\Composer\AppInstaller;

/**
 * AppInstallerTest tests the auto-installer
 */
class AppInstallerTest extends WebTestCase
{

    protected $client;
    protected $cacheDir;
    static protected $subDir = '/config/platform/';
    protected $generated;

    protected function setUp()
    {
        $this->client = self::createClient();
        $this->cacheDir = self::$kernel->getContainer()->getParameter('kernel.cache_dir');
        $baseDir = $this->cacheDir . static::$subDir;
        if (!file_exists($baseDir)) {
            mkdir($baseDir, 0777, true);
        }
        $defaultCfg['parameters'] = ['oneParam' => 'defaultValue'];
        $dest = $baseDir . 'default.yml';
        file_put_contents($dest, \Symfony\Component\Yaml\Yaml::dump($defaultCfg));
        $this->generated = $baseDir . php_uname('n') . '.yml';
        unlink($this->generated);
    }

    public function testInstall()
    {
        $package = $this->getMockBuilder('Package')
                ->setMethods(['getExtra'])
                ->getMock();
        $package->expects($this->once())
                ->method('getExtra')
                ->will($this->returnValue([
                            'symfony-app-dir' => $this->cacheDir,
        ]));

        $composer = $this->getMockBuilder('Composer')
                ->setMethods(['getPackage'])
                ->getMock();
        $composer->expects($this->once())
                ->method('getPackage')
                ->will($this->returnValue($package));

        $console = $this->getMockBuilder('Console')
                ->setMethods(['write', 'ask'])
                ->getMock();
        $console->expects($this->any())
                ->method('ask')
                ->will($this->returnValue('myValue'));

        $event = $this->getMockBuilder('Composer\Script\Event')
                ->setMethods(['getComposer', 'getIO'])
                ->getMock();
        $event->expects($this->once())
                ->method('getComposer')
                ->will($this->returnValue($composer));
        $event->expects($this->once())
                ->method('getIO')
                ->will($this->returnValue($console));

        $this->assertFileNotExists($this->generated);
        AppInstaller::installPlateform($event);
        $this->assertFileExists($this->generated);

        $customCfg = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($this->generated));
        $this->assertEquals('myValue', $customCfg['parameters']['oneParam']);
    }

}