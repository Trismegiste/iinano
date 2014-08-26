<?php

/*
 * Prelude
 */

namespace Trismegiste\Prelude\Tests\Composer;

use Trismegiste\Prelude\Composer\Script;

/**
 * ScriptTest tests the auto-installer
 */
class ScriptTest extends \PHPUnit_Framework_TestCase
{

    protected $client;
    protected $tempDir;
    static protected $subDir = '/config/platform/';
    protected $generated;

    protected function setUp()
    {
        $this->tempDir = sys_get_temp_dir();
        $baseDir = $this->tempDir . static::$subDir;
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
                ->will($this->returnValue(['symfony-app-dir' => $this->tempDir]));

        $composer = $this->getMockBuilder('Composer')
                ->setMethods(['getPackage'])
                ->getMock();
        $composer->expects($this->once())
                ->method('getPackage')
                ->will($this->returnValue($package));

        $console = $this->getMockBuilder('Composer\IO\ConsoleIO')
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
        Script::installPlatform($event);
        $this->assertFileExists($this->generated);

        $customCfg = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($this->generated));
        $this->assertEquals('myValue', $customCfg['parameters']['oneParam']);
    }

}