<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Trismegiste\OAuthBundle\Security\Token;
use Trismegiste\SocialBundle\Security\Credential\OAuth;
use Trismegiste\SocialBundle\Security\LandingPageSuccessHandler;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Socialist\Author;

/**
 * LandingPageSuccessHandlerTest tests the success login handler
 */
class LandingPageSuccessHandlerTest extends \PHPUnit_Framework_TestCase
{

    /** Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface */
    protected $sut;
    protected $urlGenerator;

    /** @var SecurityContextInterface */
    protected $security;

    protected function setUp()
    {
        $this->urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $httpUtils = new HttpUtils($this->urlGenerator);
        $this->sut = new LandingPageSuccessHandler($httpUtils, $this->security);
    }

    public function getUser()
    {
        return [
            ['ROLE_USER', 'content_index'],  // => will be intercepted by AccessDeniedListener in func test
            ['ROLE_MODERATOR', 'admin_abusive_pub_listing'],
            ['ROLE_MANAGER', 'admin_netizen_listing'],
            ['ROLE_ADMIN', 'admin_dashboard'],
            ['VALID_ACCESS', 'content_index']
        ];
    }

    /**
     * @dataProvider getUser
     */
    public function testRedirect($granted, $path)
    {
        $default = new Netizen(new Author('kirk'));
        $default->setCredential(new OAuth('1701', 'ufp'));
        $request = new Request();
        $token = new Token('secured_area', 'ufp', '1701');
        $token->setUser($default);
        $this->security->expects($this->atLeast(1))
                ->method('isGranted')
                ->will($this->returnCallback(function($role) use ($granted) {
                            return $role == $granted;
                        }));

        $this->urlGenerator->expects($this->once())
                ->method('generate')
                ->with($path)
                ->willReturn('ok');

        $response = $this->sut->onAuthenticationSuccess($request, $token);
        $cookie = $response->headers->getCookies()[0];
        $this->assertEquals('oauth_provider', $cookie->getName());
        $this->assertEquals('ufp', $cookie->getValue());
    }

}
