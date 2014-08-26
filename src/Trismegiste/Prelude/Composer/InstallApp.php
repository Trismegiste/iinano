<?php

/*
 * Prelude
 */

namespace Trismegiste\Prelude\Composer;

use Composer\IO\ConsoleIO;
use Symfony\Component\Yaml\Yaml;

/**
 * InstallApp is a CLI app for installing a Symfony app
 * (called by Composer script)
 * 
 * Main purpose: It decouples the install script from Composer
 * for easy testing/mockuping and does not violate Demeter's Law
 */
class InstallApp
{

    protected $symfonyAppDir;
    protected $composerIO;

    /**
     * Ctor
     * 
     * @param string $directory the symfony AppKernel directory
     * @param ConsoleIO $io the composer IO console
     */
    public function __construct($directory, ConsoleIO $io)
    {
        $this->symfonyAppDir = $directory;
        $this->composerIO = $io;
    }

    /**
     * Runs the app
     */
    public function execute()
    {
        $plateformDir = $this->symfonyAppDir . '/config/platform/';
        $template = $plateformDir . 'default.yml';
        $platformName = static::getPlatformName();
        $dest = $plateformDir . $platformName . '.yml';
        if (!file_exists($dest)) {
            $this->composerIO->write("<info>Configuring parameters for $platformName :</info>");

            $defaultParam = Yaml::parse($template);
            foreach ($defaultParam['parameters'] as $key => $val) {
                $override = $this->composerIO->ask("<question>$key</question> [$val] = ", $val);
                $newValues[$key] = $override;
            }
            $newConfig['parameters'] = $newValues;
            file_put_contents($dest, Yaml::dump($newConfig));

            $this->composerIO->write("Writing parameters to <comment>$dest</comment>");
        }
    }

    /**
     * Gets the platform's name
     * 
     * @return string
     */
    static public function getPlatformName()
    {
        return php_uname('n');
    }

}