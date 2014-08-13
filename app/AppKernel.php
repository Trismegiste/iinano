<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Trismegiste\DokudokiBundle\TrismegisteDokudokiBundle(),
            new Trismegiste\SocialBundle\TrismegisteSocialBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
        $loader->import(__DIR__ . '/config/platform/' . php_uname('n') . '.yml');
    }

    /**
     * Injecting the owner's name of this AppKernel file to add an "env parameter"
     * Note : This mechanism is only avalaible in DEV because it is only intended
     * in development environment.
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
