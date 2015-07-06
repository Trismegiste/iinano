<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security;

use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Trismegiste\OAuthBundle\Oauth\ThirdPartyAuthentication;
use Trismegiste\OAuthBundle\Security\Token;
use Trismegiste\SocialBundle\Security\NotRegisteredHandler;

/**
 * NotRegisteredHandlerTest tests the failure login handler
 */
class NotRegisteredHandlerTest extends \PHPUnit_Framework_TestCase
{

    /** @var AuthenticationFailureHandlerInterface */
    protected $sut;
    protected $urlGenerator;
    protected $request;
    protected $session;

    protected function setUp()
    {
        $this->urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $httpUtils = new HttpUtils($this->urlGenerator);
        $this->sut = new NotRegisteredHandler($httpUtils, new NullLogger());
        $this->request = new Request();
        $this->session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $this->request->setSession($this->session);
    }

    public function testNoToken()
    {
        $exception = new BadCredentialsException();

        $this->urlGenerator->expects($this->once())
                ->method('generate')
                ->with('trismegiste_oauth_connect')
                ->willReturn('ok');
        $this->session->expects($this->once())
                ->method('set')
                ->with(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

        $this->sut->onAuthenticationFailure($this->request, $exception);
    }

    public function testWithToken()
    {
        $token = new Token('secured_area', 'ufp', '1701', [ThirdPartyAuthentication::IDENTIFIED]);

        $exception = new BadCredentialsException('unknow', 123, new UsernameNotFoundException());
        $exception->setToken($token);

        $this->urlGenerator->expects($this->once())
                ->method('generate')
                ->with('guest_register')
                ->willReturn('ok');
        $this->session->expects($this->once())
                ->method('set')
                ->with(NotRegisteredHandler::IDENTIFIED_TOKEN, $token);

        $this->sut->onAuthenticationFailure($this->request, $exception);
    }

}
