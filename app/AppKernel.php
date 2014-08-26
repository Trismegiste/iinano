<?php

use Trismegiste\Prelude\Kernel;

class AppKernel extends Kernel
{

    protected function registerAdditionalBundles()
    {
        return [
            new Trismegiste\DokudokiBundle\TrismegisteDokudokiBundle(),
            new Trismegiste\SocialBundle\TrismegisteSocialBundle()
        ];
    }

}
