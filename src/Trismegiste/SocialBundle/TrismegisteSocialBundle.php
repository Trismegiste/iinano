<?php

namespace Trismegiste\SocialBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Application;

class TrismegisteSocialBundle extends Bundle
{

    /**
     * SRSLY ?
     */
    public function getContainerExtension()
    {
        return new DependencyInjection\TrismegisteSocialExtension();
    }

    /**
     * SRSLY ?
     */
    public function registerCommands(Application $application)
    {
        $application->addCommands([
            new Cli\CreateUser()
        ]);
    }

}
