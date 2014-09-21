<?php

namespace Trismegiste\SocialBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class Extension extends BaseExtension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        // inject parameters from the validated config of this bundle
        // alias for the netizen class :
        $container->getDefinition('social.netizen.repository')
                ->replaceArgument(2, $config['alias']['user']);
        // list of aliases for content classes :
        $container->getDefinition('social.content.repository')
                ->addArgument($config['alias']['content']);
        // injecting the regex for validation of nickname (dry) :
        $container->setParameter('nickname_regex', $config['nickname_regex']);
        // injecting how many contents inside a page :
        $container->setParameter('social.pagination', $config['pagination']);
        // avatar size :
        $container->setParameter('social.avatar_size', $config['avatar_size']);
        $container->getDefinition('social.avatar.repository')
                ->replaceArgument(2, $config['avatar_size']);
    }

    public function getAlias()
    {
        return 'iinano';
    }

}
