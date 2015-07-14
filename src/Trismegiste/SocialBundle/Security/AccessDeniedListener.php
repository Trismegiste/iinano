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
 * AccessDeniedListener catches access denied exception and redirect to payment
 * only if the user is valid and his ticket is invalid. 403s caused by security breach
 * or firewall don't redirect to payment
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
        // $session = $event->getRequest()->getSession(); @todo remove session from ctor and use request

        if ($exception instanceof AccessDeniedHttpException) {
            $token = $this->security->getToken();
            if ($token instanceof Token) {
                $user = $token->getUser();
                if ($user instanceof Netizen) {
                    if (!$this->security->isGranted(TicketVoter::SUPPORTED_ATTRIBUTE)) {
                        $this->session->getFlashBag()->add('warning', 'Your subscribing has expired');
                        $response = new RedirectResponse($this->router->generate('buy_new_ticket')); // @todo parameter please
                        $event->setResponse($response);
                    }
                }
            }
        }
    }

}
