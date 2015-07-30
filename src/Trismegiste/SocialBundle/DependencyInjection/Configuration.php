<?php

namespace Trismegiste\SocialBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('trismegiste_social');

        $rootNode->children()
                    ->scalarNode('nickname_regex')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('pagination')->defaultValue(20)->end()
                    ->scalarNode('commentary_preview')->defaultValue(3)->end()
                    ->arrayNode('dynamic_default')
                        ->useAttributeAsKey('key')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                    ->scalarNode('bandwidth')
                        ->defaultValue('eth0')
                        ->validate()
                            ->ifTrue(function($node) { return !is_null($node);} )
                            ->then(function($node) {
                                    if (empty(shell_exec('which vnstat'))) {
                                        throw new InvalidConfigurationException("vnstat is not installed");
                                    }
                                    if (preg_match('#^Error#', shell_exec('vnstat -i ' . $node))) {
                                        throw new InvalidConfigurationException("Network interface '$node' does not exist");
                                    }
                                    return $node;
                                })
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
