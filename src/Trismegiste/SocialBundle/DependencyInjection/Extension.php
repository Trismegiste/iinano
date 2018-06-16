<?php

namespace Trismegiste\SocialBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class Extension extends BaseExtension implements PrependExtensionInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('security.xml');
        $loader->load('repository.xml');
        $loader->load('services.xml');
        $loader->load('forms.xml');

        // injecting the regex for validation of nickname (dry) :
        $container->setParameter('nickname_regex', $config['nickname_regex']);
        // injecting how many contents inside a page :
        $container->setParameter('social.pagination', $config['pagination']);
        // limit of preview for commentaries :
        $container->setParameter('social.commentary_preview', $config['commentary_preview']);
        // inject default config in dynamic config
        $container->getDefinition('social.dynamic_config')
                ->replaceArgument(2, $config['dynamic_default']);
        // inject network interface name in server status service for monitoring its bandwidth
        $networkInterface = $config['quota']['bandwidth']['name'];
      //  $this->checkVnStatConfig($networkInterface);
        $container->getDefinition('server.status')
                ->replaceArgument(0, $networkInterface);
        // inject storage quota for db
        $container->getDefinition('database.status')
                ->replaceArgument(1, $config['quota']['storage']['database']);
        // capped limit for commentaries collection :
        $container->getDefinition('social.publishing.repository')
                ->replaceArgument(3, $config['commentary_limit']);
    }

    public function getAlias()
    {
        return 'iinano';
    }

    /**
     * Not in config class because even if the node is set to the default,
     * vnstat installation must be checked upon
     *
     * @param string $inet
     *
     * @throws InvalidConfigurationException
     */
    private function checkVnStatConfig($inet)
    {
        if (empty(shell_exec('which vnstat'))) {
            throw new InvalidConfigurationException("vnstat is not installed: https://github.com/vergoh/vnstat");
        }
        if (preg_match('#^Error#', shell_exec('vnstat -i ' . $inet))) {
            throw new InvalidConfigurationException("Network interface '$inet' does not exist");
        }
    }

    /**
     * Inject quota param into twig globals
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->prependExtensionConfig('twig', ['globals' => ['app_quota' => $config['quota']]]);
    }

}
