<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * ImportAliases is a compiler pass to import some Dokudoki aliases definitions
 * in this services bundle.
 */
class ImportAliases implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $aliasCfg = $container->getDefinition('dokudoki.builder.whitemagic')->getArgument(0);
        $content = [];
        foreach ($aliasCfg as $key => $fqcn) {
            if (is_subclass_of($fqcn, 'Trismegiste\Socialist\Publishing', true)) {
                $content[] = $key;
            } else if (is_subclass_of($fqcn, 'Trismegiste\Socialist\User', true)) {
                $userAlias = $fqcn;
            }
        }
        // content
        if (count($content)) {
            $container->getDefinition('social.content.repository')
                    ->replaceArgument(2, $content);
        } else {
            throw new InvalidConfigurationException("No alias defined in Dokudoki is a subclass of Publishing");
        }
        // user alias
        if (isset($userAlias)) {
            $container->getDefinition('social.netizen.repository')
                    ->replaceArgument(2, $userAlias);
        } else {
            throw new InvalidConfigurationException("No alias defined in Dokudoki is a subclass of User");
        }
    }

}
