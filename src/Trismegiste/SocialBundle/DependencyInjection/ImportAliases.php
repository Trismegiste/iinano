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
 * into this bundle services.
 */
class ImportAliases implements CompilerPassInterface
{

    /**
     * Import alias from Dokudoki WhiteMagic Stage into various services (repository,
     * factories, twig renderer...)
     *
     * @param ContainerBuilder $container
     *
     * @throws InvalidConfigurationException
     */
    public function process(ContainerBuilder $container)
    {
        $aliasCfg = $container->getDefinition('dokudoki.builder.whitemagic')->getArgument(0);
        $this->injectContent($container, $aliasCfg);
        $this->injectUser($container, $aliasCfg);
        $this->injectPrivateMessage($container, $aliasCfg);
    }

    private function injectContent(ContainerBuilder $container, array $aliasCfg)
    {
        $contentAlias = [];
        foreach ($aliasCfg as $key => $fqcn) {
            if (is_subclass_of($fqcn, 'Trismegiste\Socialist\Publishing', true)) {
                $contentAlias[$key] = $fqcn;
            }
        }

        if (!count($contentAlias)) {
            throw new InvalidConfigurationException("No alias defined in Dokudoki is a subclass of Publishing");
        }

        // content aliases for repository of publishing :
        $container->getDefinition('social.content.repository')
                ->replaceArgument(2, array_keys($contentAlias));
        // url param regex for CRUD operation on Publishing :
        $container->setParameter('crud_url_param_regex', '(' . implode('|', array_keys($contentAlias)) . ')');
        // content aliases into twig RendererExtension :
        $container->getDefinition('twig.social.renderer')
                ->replaceArgument(1, $contentAlias);
        // content aliases into Crud form factory :
        $container->getDefinition('social.form.factory')
                ->replaceArgument(2, $contentAlias);
    }

    private function injectUser(ContainerBuilder $container, array $aliasCfg)
    {
        $userAlias = [];
        foreach ($aliasCfg as $key => $fqcn) {
            if (is_subclass_of($fqcn, 'Trismegiste\Socialist\User', true)) {
                $userAlias[] = $key;
            }
        }

        if (1 !== count($userAlias)) {
            throw new InvalidConfigurationException("Only one alias of a subclass of User is permitted in Dokudoki configuration");
        }

        // user alias for netizen repository :
        $container->getDefinition('social.netizen.repository')
                ->replaceArgument(2, $userAlias[0]);
    }

    private function injectPrivateMessage(ContainerBuilder $container, array $aliasCfg)
    {
        $pmAlias = [];
        foreach ($aliasCfg as $key => $fqcn) {
            if ($fqcn === 'Trismegiste\Socialist\PrivateMessage') {
                $pmAlias[] = $key;
            }
        }

        if (1 !== count($pmAlias)) {
            print_r($pmAlias);
            throw new InvalidConfigurationException("Only one alias of a subclass of PrivateMessage is permitted in Dokudoki configuration");
        }

        // private message alias for PrivateMessageRepository :
        $container->getDefinition('social.private_message.repository')
                ->replaceArgument(2, $pmAlias[0]);
    }

}
