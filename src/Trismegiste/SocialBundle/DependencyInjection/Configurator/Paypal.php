<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\DependencyInjection\Configurator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Trismegiste\SocialBundle\Config\Provider;
use Trismegiste\SocialBundle\Payment\Gateway;

/**
 * Configurator for paypal service
 */
class Paypal
{

    protected $config;
    protected $urlGenerator;
    protected $cancelRoute;
    protected $successRoute;

    public function __construct(Provider $config, UrlGeneratorInterface $urlGenerator, $success, $cancel)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->cancelRoute = $cancel;
        $this->successRoute = $success;
    }

    public function configure(Gateway $gateway)
    {
        $conf = $config['paypal'];
        $conf['RETURNURL'] = $this->urlGenerator->generate($this->successRoute);
        $conf['CANCELURL'] = $this->urlGenerator->generate($this->cancelRoute);
        $gateway->setConfig($conf);
    }

}
