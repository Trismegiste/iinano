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
        $contentAlias = [];
        $userAlias = [];
        foreach ($aliasCfg as $key => $fqcn) {
            if (is_subclass_of($fqcn, 'Trismegiste\Socialist\Publishing', true)) {
                $contentAlias[] = $key;
            } else if (is_subclass_of($fqcn, 'Trismegiste\Socialist\User', true)) {
                $userAlias[] = $key;
            }
        }
        // content :
        if (count($contentAlias)) {
            $container->getDefinition('social.content.repository')
                    ->replaceArgument(2, $contentAlias);
        } else {
            throw new InvalidConfigurationException("No alias defined in Dokudoki is a subclass of Publishing");
        }
        // user alias :
        if (1 === count($userAlias)) {
            $container->getDefinition('social.netizen.repository')
                    ->replaceArgument(2, $userAlias[0]);
        } else {
            throw new InvalidConfigurationException(count($userAlias) . " alias(es) defined in Dokudoki is(are) a subclass of User, only one is authorized");
        }
        // url param regex for CRUD operation on Publishing :
        $container->setParameter('crud_url_param_regex', '(' . implode('|', $contentAlias) . ')');
    }

}
