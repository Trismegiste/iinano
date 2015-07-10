<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\OAuthBundle\Security\Token;

/**
 * AccessDeniedListener is a ...
 */
class AccessDeniedListener
{

    protected $security;
    protected $router;
    protected $session;

    public function __construct(SecurityContextInterface $secu, UrlGeneratorInterface $router, SessionInterface $sess)
    {
        $this->security = $secu;
        $this->router = $router;
        $this->session = $sess;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof AccessDeniedHttpException) {
            $token = $this->security->getToken();
            if ($token instanceof Token) {
                $user = $token->getUser();
                if ($user instanceof Netizen) {
                    if (!$this->security->isGranted(TicketVoter::SUPPORTED_ATTRIBUTE)) {
                        $this->session->getFlashBag()->add('warning', 'Your subscribing has expired');
                        $response = new RedirectResponse($this->router->generate('buy_new_ticket'));
                        $event->setResponse($response);
                    }
                }
            }
        }
    }

}
