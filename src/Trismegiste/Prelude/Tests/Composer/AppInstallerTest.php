<?php

/*
 * Prelude
 */

namespace Trismegiste\Prelude\Tests\Composer;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trismegiste\Prelude\Composer\AppInstaller;

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
        // make sure old tests are deleted
        if (file_exists($this->generated)) {
            unlink($this->generated);
        }
    }

    public function testInstall()
    {
        $package = $this->getMockBuilder('Package')
                ->setMethods(['getExtra'])
                ->getMock();
        $package->expects($this->once())
                ->method('getExtra')
                ->will($this->returnValue([
                            // I will generate the config in the cache. It's a little ugly
                            // but who cares ? Furthermore, it's only in the test environment
                            // so there's no security issue
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
        // after so many mockup, this is what I call the undoubtful proof for a violation of Demeter's law somewhere
        // Remember, everytime you broke Demeter's law, God kills a kitten. Please think of the kitten

        $this->assertFileNotExists($this->generated);
        AppInstaller::installPlatform($event);
        $this->assertFileExists($this->generated);

        $customCfg = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($this->generated));
        $this->assertEquals('myValue', $customCfg['parameters']['oneParam']);
    }

}