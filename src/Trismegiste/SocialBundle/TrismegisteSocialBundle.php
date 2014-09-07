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
            new Cli\CreateUser()
        ]);
        if ($this->container->getParameter('kernel.environment') == 'dev') {
            $application->add(new Cli\FillWithDummy());
        }
    }

}
