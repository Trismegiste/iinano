<?php

namespace Trismegiste\SocialBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Application;

class TrismegisteSocialBundle extends Bundle
{

    /**
     * KISS
     */
    public function getContainerExtension()
    {
        return new DependencyInjection\Extension();
    }

    /**
     * KISS
     */
    public function registerCommands(Application $application)
    {
        $application->addCommands([
            new Cli\CreateUser(),
            new Cli\NormalizeDatabase(),
            new Cli\FillForBench()
        ]);
        if ($this->container->getParameter('kernel.environment') == 'dev') {
            $application->add(new Cli\FillWithDummy());
        }
    }

    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DependencyInjection\ImportAliases());
    }

}
