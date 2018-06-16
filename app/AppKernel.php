<?php

use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends SymfonyKernel {

    public function registerContainerConfiguration(LoaderInterface $loader) {
        $loader->load($this->rootDir . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    public function registerBundles() {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Trismegiste\DokudokiBundle\TrismegisteDokudokiBundle(),
            new Trismegiste\OAuthBundle\TrismegisteOAuthBundle(),
            new Trismegiste\SocialBundle\TrismegisteSocialBundle()
        ];

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        return $bundles;
    }

}
