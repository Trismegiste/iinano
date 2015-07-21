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
    protected $paymentRoute;

    public function __construct(SecurityContextInterface $secu, UrlGeneratorInterface $router, $paymentRoute)
    {
        $this->security = $secu;
        $this->paymentRoute = $router->generate($paymentRoute);
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        $session = $request->getSession();

        if ($exception instanceof AccessDeniedHttpException) {
            // no redirection on ajax, could be messy :
            if (!$request->isXmlHttpRequest()) {
                $token = $this->security->getToken();
                if ($token instanceof Token) {
                    $user = $token->getUser();
                    if ($user instanceof Netizen) {
                        if (!$this->security->isGranted(TicketVoter::SUPPORTED_ATTRIBUTE)) {
                            // we redirect to payment if it is a 403 Error
                            // with a standard html request with OAuth user with an invalid ticket
                            $session->getFlashBag()->add('warning', 'Your subscribing has expired');
                            $response = new RedirectResponse($this->paymentRoute);
                            $event->setResponse($response);
                            // for example, accessing to forbidden resource with a valid ticket don't redirect to payment
                            // the same if its a ajax request, an unauthenticated user (raw 403) etc...
                        }
                    }
                }
            }
        }
    }

}
