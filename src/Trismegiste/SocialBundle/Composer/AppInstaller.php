<?php

/*
 * sf2ffbp
 */

namespace Trismegiste\SocialBundle\Composer;

/**
 * AppInstaller is ...
 *
 * @author flo
 */
class AppInstaller
{

    static public function installPlateform($event)
    {
        $cfg = $event->getComposer()->getPackage()->getExtra();
        $plateformDir = $cfg['symfony-app-dir'] . '/config/platform/';
        $template = $plateformDir . 'default.yml';
        $dest = $plateformDir . php_uname('n') . '.yml';
        if (!file_exists($dest)) {
            $console = $event->getIO();
            $console->write('<info>Configuring plateform-specific parameters :</info>');

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