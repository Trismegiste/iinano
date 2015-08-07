<?php

namespace Trismegiste\SocialBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TrismegisteSocialBundle extends Bundle
{

    /**
     * KISS
     */
    public function getContainerExtension()
    {
        if (is_null($this->extension)) {
            $this->extension = new DependencyInjection\Extension();
        }

        return $this->extension;
    }

    /**
     * KISS
     */
    public function registerCommands(Application $application)
    {
        $application->addCommands([
            new Cli\NormalizeDatabase(),
            new Cli\CliqueBench(),
            new Cli\MapReduceJob(),
            new Cli\ClearStorage(),
            new Cli\ClearDatabase(),
        ]);
        if ($this->container->getParameter('kernel.environment') == 'dev') {
            $application->add(new Cli\FillWithDummy());
        }
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DependencyInjection\ImportAliases());
    }

}
