<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\OAuthBundle\Security\Token;
use Trismegiste\SocialBundle\Security\AccessDeniedListener;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\SocialBundle\Security\TicketVoter;
use Trismegiste\Socialist\Author;

/**
 * AccessDeniedListenerTest tests the listener of 403 and checks where the user is redirected
 */
class AccessDeniedListenerTest extends \PHPUnit_Framework_TestCase
{

    /** @var AccessDeniedListener */
    protected $sut;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var SecurityContextInterface */
    protected $security;

    /** @var SessionInterface */
    protected $session;

    protected function setUp()
    {
        $this->urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');

        $this->sut = new AccessDeniedListener($this->security, $this->urlGenerator, $this->session);
    }

    protected function createEvent(\Exception $e)
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $request = new Request();
        $event = new GetResponseForExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $e);

        return $event;
    }

    public function testBadException()
    {
        $e = new AccessDeniedException();  // not an AccessDeniedHttpException
        $event = $this->createEvent($e);

        $this->sut->onKernelException($event);
        $this->assertFalse($event->hasResponse());
    }

    public function testUnauthenticatedWithOAuthToken()
    {
        $event = $this->createEvent(new AccessDeniedHttpException());
        $this->security->expects($this->once())
                ->method('getToken');

        $this->sut->onKernelException($event);
        $this->assertFalse($event->hasResponse());
    }

    public function testAuthenticatedWithOAuthTokenButWithoutNetizen()
    {
        $event = $this->createEvent(new AccessDeniedHttpException());
        $this->security->expects($this->once())
                ->method('getToken')
                ->willReturn(new Token('secu', 'dummy', '123456'));

        $this->sut->onKernelException($event);
        $this->assertFalse($event->hasResponse());
    }

    public function testAuthenticatedWithValidNetizen()
    {
        $token = new Token('secu', 'dummy', '123456');
        $user = new Netizen(new Author('kirk'));
        $token->setUser($user);

        $event = $this->createEvent(new AccessDeniedHttpException());
        $this->security->expects($this->once())
                ->method('getToken')
                ->willReturn($token);
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with(TicketVoter::SUPPORTED_ATTRIBUTE)
                ->willReturn(true);

        $this->sut->onKernelException($event);
        $this->assertFalse($event->hasResponse());
    }

    public function testAuthenticatedWithInvalidNetizen()
    {
        $token = new Token('secu', 'dummy', '123456');
        $user = new Netizen(new Author('kirk'));
        $token->setUser($user);

        $event = $this->createEvent(new AccessDeniedHttpException());
        $this->security->expects($this->once())
                ->method('getToken')
                ->willReturn($token);
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with(TicketVoter::SUPPORTED_ATTRIBUTE)
                ->willReturn(false);

        $bag = new \Symfony\Component\HttpFoundation\Session\Flash\FlashBag();
        $this->session->expects($this->once())
                ->method('getFlashBag')
                ->willReturn($bag);

        $this->sut->onKernelException($event);
        $this->assertTrue($event->hasResponse());
        $this->assertCOunt(1, $bag);
    }

}
