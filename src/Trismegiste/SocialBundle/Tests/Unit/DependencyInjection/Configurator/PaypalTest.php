<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\DependencyInjection\Configurator;

use Trismegiste\SocialBundle\DependencyInjection\Configurator\Paypal;

/**
 * PaypalTest tests the configurator for paypal service
 */
class PaypalTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $config = [
            'paypal' => [
                'username' => 'uuu',
                'password' => 'ppp',
                'signature' => 'sss',
                'sandbox' => true
            ],
            'appTitle' => 'iinano'
        ];
        $urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');

        $this->sut = new Paypal(new \ArrayIterator($config), $urlGenerator, 'success', 'cancel');
    }

    public function testConfigureService()
    {
        $gateway = $this->getMock('Trismegiste\SocialBundle\Payment\Gateway');
        $gateway->expects($this->once())
                ->method('setConfig')
                ->with([
                    'appTitle' => 'iinano',
                    'username' => 'uuu',
                    'password' => 'ppp',
                    'signature' => 'sss',
                    'sandbox' => true,
                    'return_url' => null,
                    'cancel_url' => null
        ]);

        $this->sut->configure($gateway);
    }

}
