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
        $conf = [
            'username' => 'trismegiste-facilitator_api1.voila.fr',
            'password' => 'UUEMF2XQL4EX3TYJ',
            'signature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31Ar98jnDSdjKrfA12tKK25f9kqu5Q',
            'sandbox' => true
        ];
        //    $this->config['paypal'];
        $conf['return_url'] = $this->urlGenerator->generate($this->successRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);
        $conf['cancel_url'] = $this->urlGenerator->generate($this->cancelRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);
        $gateway->setConfig($conf);
    }

}
