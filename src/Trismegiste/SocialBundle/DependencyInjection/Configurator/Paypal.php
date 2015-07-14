<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\DependencyInjection\Configurator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    public function __construct(\ArrayAccess $config, UrlGeneratorInterface $urlGenerator, $success, $cancel)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->cancelRoute = $cancel;
        $this->successRoute = $success;
    }

    public function configure(Gateway $gateway)
    {
        $paypal = $this->config['paypal'];
        if (!is_null($paypal)) {
            $conf = [
                'username' => $paypal['username'],
                'password' => $paypal['password'],
                'signature' => $paypal['signature'],
                'sandbox' => (bool) $paypal['sandbox']
            ];
        }

        $conf['appTitle'] = $this->config['appTitle'];
        $conf['return_url'] = $this->urlGenerator->generate($this->successRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);
        $conf['cancel_url'] = $this->urlGenerator->generate($this->cancelRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);

        $gateway->setConfig($conf);
    }

}
