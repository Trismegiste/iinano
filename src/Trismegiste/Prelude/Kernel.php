<?php

/*
 * Prelude
 */

namespace Trismegiste\Prelude;

use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * This is a Template Method for a Symfony Kernel
 */
abstract class Kernel extends SymfonyKernel
{

    abstract protected function registerAdditionalBundles();

    final public function registerBundles()
    {
        $bundles = array_merge([
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle()
                ], $this->registerAdditionalBundles());

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        return $bundles;
    }

    /**
     * Loading the config for the current evironment 
     * and importing platform-specific parameters depending of computer's name
     * 
     * With this system you can store/commit/track (or not with .gitignore) every change in
     * every config on every computer. Reduce the rate of "But it works on my computer !"
     * 
     * I prefer this system than one from Incenteev but you can mix both
     * 
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->rootDir . '/config/config_' . $this->getEnvironment() . '.yml');
        $loader->import($this->rootDir . '/config/platform/' . php_uname('n') . '.yml');
    }

    /**
     * Injecting the owner's name of this Kernel file to add an "env parameter"
     * Note : This mechanism is only avalaible in DEV because it is only intended
     * in development environment. With this parameter, you can customize any
     * parameter of config_dev.yml with the shortcut %developer.name% 
     * 
     * This is very convenient when you have many developer on one common
     * development linux server.
     *
     * @return array
     */
    protected function getEnvParameters()
    {
        $base = parent::getEnvParameters();
        // owner of source
        if ('dev' == $this->getEnvironment()) {
            $base['developer.name'] = 'dev';

            if (function_exists('posix_getpwuid')) {
                $owner = posix_getpwuid(fileowner(__FILE__));
                if (array_key_exists('name', $owner)) {
                    $base['developer.name'] = $owner['name'];
                }
            }
        }

        return $base;
    }

}
