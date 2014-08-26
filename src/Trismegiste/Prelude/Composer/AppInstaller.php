<?php

/*
 * Prelude
 */

namespace Trismegiste\Prelude\Composer;

use Composer\Script\Event;

/**
 * AppInstaller is an auto-installer for platform specific parameters
 */
class AppInstaller
{

    static public function installPlatform(Event $event)
    {
        $cfg = $event->getComposer()->getPackage()->getExtra();
        $plateformDir = $cfg['symfony-app-dir'] . '/config/platform/';
        $template = $plateformDir . 'default.yml';
        $platformName = php_uname('n');
        $dest = $plateformDir . $platformName . '.yml';
        if (!file_exists($dest)) {
            $console = $event->getIO();
            $console->write("<info>Configuring parameters for $platformName :</info>");

            $defaultParam = \Symfony\Component\Yaml\Yaml::parse($template);
            foreach ($defaultParam['parameters'] as $key => $val) {
                $override = $console->ask("<question>$key</question> [$val] = ", $val);
                $newValues[$key] = $override;
            }
            $newConfig['parameters'] = $newValues;
            file_put_contents($dest, \Symfony\Component\Yaml\Yaml::dump($newConfig));

            $console->write("Writing parameters to <comment>$dest</comment>");
        }
    }

}