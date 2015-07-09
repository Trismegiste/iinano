<?php

use Trismegiste\Prelude\Kernel;

class AppKernel extends Kernel
{

    protected function registerAdditionalBundles()
    {
        $set = [
            new Trismegiste\DokudokiBundle\TrismegisteDokudokiBundle(),
            new Trismegiste\OAuthBundle\TrismegisteOAuthBundle(),
            new Trismegiste\SocialBundle\TrismegisteSocialBundle()
        ];
        if ($this->debug) {
            $set[] = new Trismegiste\KoyaScanBundle\TrismegisteKoyaScanBundle();
        }

        return $set;
    }

}
