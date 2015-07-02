<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Trismegiste\OAuthBundle\Oauth\ThirdPartyAuthentication;
use Trismegiste\OAuthBundle\Security\Token;

/**
 * NotRegisteredHandler is a handler whe the user authentified by OAuth is not registered
 * in iinano system
 */
class NotRegisteredHandler implements AuthenticationFailureHandlerInterface
{

    const IDENTIFIED_TOKEN = "identified_stored_token";

    protected $httpUtils;
    protected $logger;

    public function __construct(HttpUtils $httpUtils, LoggerInterface $logger)
    {
        $this->httpUtils = $httpUtils;
        $this->logger = $logger;
        $this->failureDefault = 'trismegiste_oauth_connect';
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $targetPath = $this->failureDefault;
        $this->logger->debug('Authentication failure handled by ' . __CLASS__, [
            $exception,
            $exception->getPrevious(),
            $exception->getToken()
        ]);

        if (($exception instanceof BadCredentialsException) &&
                ($exception->getPrevious() instanceof UsernameNotFoundException) &&
                ($exception->getToken() instanceof Token) &&
                ($exception->getToken()->getRoles()[0]->getRole() == ThirdPartyAuthentication::IDENTIFIED)) {
            $this->logger->info('Go to register');
            $targetPath = 'guest_register';
            $request->getSession()->set(self::IDENTIFIED_TOKEN, $exception->getToken());
        } else {
            $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);
        }

        return $this->httpUtils->createRedirectResponse($request, $targetPath);
    }

}
