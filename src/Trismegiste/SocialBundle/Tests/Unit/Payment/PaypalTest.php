<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit;

use Trismegiste\SocialBundle\Payment\Paypal;

/**
 * PaypalTest paypal client
 */
class PaypalTest extends \PHPUnit_Framework_TestCase
{

    /** @var Paypal */
    protected $sut;

    /** @var \Payum\Paypal\ExpressCheckout\Nvp\Api */
    protected $apiMock;
    protected $session;
    protected $repository;
    protected $logger;
    protected $security;

    protected function setUp()
    {
        $this->apiMock = $this->getMockBuilder('Payum\Paypal\ExpressCheckout\Nvp\Api')
                ->disableOriginalConstructor()
                ->getMock();
        $this->session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $this->security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');
        $this->repository = $this->getMockBuilder('Trismegiste\SocialBundle\Repository\TicketRepository')
                ->disableOriginalConstructor()
                ->getMock();

        $this->sut = $this->getMockBuilder('Trismegiste\SocialBundle\Payment\Paypal')
                ->setConstructorArgs([$this->session, $this->security, $this->repository, $this->logger])
                ->setMethods(['createApi'])
                ->getMock();
        $this->sut->expects($this->any())
                ->method('createApi')
                ->willReturn($this->apiMock);
    }

    /**
     * @expectedException \Trismegiste\SocialBundle\Payment\PaymentMessage
     * @expectedExceptionMessage No fee is configured
     */
    public function testGetUrlToGatewayWithNoFee()
    {
        $this->sut->getUrlToGateway();
    }

}
