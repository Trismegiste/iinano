<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Payment;

use LogicException;
use Payum\Core\Exception\Http\HttpException;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\SocialBundle\Repository\TicketRepository;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\SocialBundle\Ticket\EntranceFee;

/**
 * Paypal Express checkout
 */
class Paypal implements Gateway
{

    const PAYPAL_TOKEN = 'paypal_url_token';

    protected $apiConfig;

    /** @var SessionInterface */
    protected $session;

    /** @var TicketRepository */
    protected $repository;

    /** @var LoggerInterface */
    protected $logger;

    /** @var SecurityContextInterface */
    protected $security;

    /**
     * Ctor
     *
     * @param SessionInterface $sess
     * @param SecurityContextInterface $secu
     * @param TicketRepository $repo
     * @param LoggerInterface $logger
     */
    public function __construct(SessionInterface $sess, SecurityContextInterface $secu, TicketRepository $repo, LoggerInterface $logger)
    {
        $this->session = $sess;
        $this->repository = $repo;
        $this->logger = $logger;
        $this->security = $secu;
    }

    public function setConfig(array $cfg)
    {
        $cfg['useraction'] = Api::USERACTION_COMMIT;
        $this->apiConfig = $cfg;
    }

    /**
     * Gets the url to the payment gateway
     *
     * @return string the url to put in a paypal button
     *
     * @throws PaymentMessage
     */
    public function getUrlToGateway()
    {
        $api = $this->createApi();
        $fee = $this->getEntranceFee();

        try {
            $response = $api->setExpressCheckout([
                'PAYMENTREQUEST_0_AMT' => $fee->getAmount(),
                'PAYMENTREQUEST_0_CURRENCYCODE' => $fee->getCurrency(),
                'PAYMENTREQUEST_0_DESC' => sprintf('Access to %s for %d months'
                        , $this->apiConfig['appTitle']
                        , $fee->getDurationValue()),
                'PAYMENTREQUEST_0_PAYMENTACTION' => Api::PAYMENTACTION_SALE,
                'NOSHIPPING' => Api::NOSHIPPING_NOT_DISPLAY_ADDRESS,
                'ALLOWNOTE' => 0
            ]);
        } catch (HttpException $e) {
            $this->logger->critical('Paypal is not responding ' . $e->getMessage());
            throw new PaymentMessage('Unreachable payment gateway: ' . $e->getMessage());
        }

        if (!$response) {
            throw new PaymentMessage('Incorrect response from payment gateway');
        }
        if ($response['ACK'] !== Api::ACK_SUCCESS) {
            throw new PaymentMessage($response['L_LONGMESSAGE0']);
        }

        $token = $response['TOKEN'];
        $this->session->set(self::PAYPAL_TOKEN, $token);

        return $api->getAuthorizeTokenUrl($token);
    }

    /**
     * Basic checks if the redirection request from paypal is ok
     *
     * @param Request $request
     *
     * @throws PaymentMessage
     */
    protected function validateReturn(Request $request)
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof Netizen) {
            throw new PaymentMessage('User is not logged'); // or hacking
        }

        if (!$request->query->has('token')) {
            throw new PaymentMessage('Invalid token from payment gateway');
        }

        $token = $request->query->get('token');
        if ($token !== $this->session->get(self::PAYPAL_TOKEN)) {
            throw new PaymentMessage('Session has expired'); // or hacking
        }
    }

    /**
     * Process the request from the redirection of paypal
     *
     * @param Request $request
     *
     * @return string transaction id
     *
     * @throws PaymentMessage error to print
     */
    public function processReturnFromGateway(Request $request)
    {
        $this->validateReturn($request);

        $token = $request->query->get('token');
        $api = $this->createApi();

        $details = $api->getExpressCheckoutDetails(['TOKEN' => $token]);
        $this->logger->debug('paypal::getdetail', $details);

        if (Api::ACK_SUCCESS != $details['ACK'] ||
                empty($details['PAYERID']) ||
                ($details['PAYMENTREQUEST_0_AMT'] == 0)) {
            throw new PaymentMessage('Invalid details of payment');
        }

        if (Api::CHECKOUTSTATUS_PAYMENT_ACTION_NOT_INITIATED != $details['CHECKOUTSTATUS']) {
            throw new PaymentMessage("Payment already done");
        }

        $response = $api->doExpressCheckoutPayment($details);
        $this->logger->debug('paypal::doPayment', $response);

        if (Api::ACK_SUCCESS == $response['ACK']) {
            $this->persistance($response['PAYMENTINFO_0_TRANSACTIONID']
                    , $details['PAYERID']
                    , $details['EMAIL']);
        }

        return $response['PAYMENTINFO_0_TRANSACTIONID'];
    }

    /**
     * Persists the new ticket
     *
     * @param string $transactionId
     * @param string $payerId
     * @param string $payerEmail
     */
    protected function persistance($transactionId, $payerId, $payerEmail)
    {
        $this->session->remove(self::PAYPAL_TOKEN);
        $ticket = $this->repository->createTicketFromPayment();

        $ticket->setTransactionInfo([
            'transactionId' => $transactionId,
            'payerId' => $payerId,
            'payerEmail' => $payerEmail
        ]);
        // save payment
        try {
            $this->repository->persistNewPayment($ticket);
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Payment was not saved for user %s transaction %s (reason: %s)')
                    , $this->getUser()->getUsername()
                    , $transactionId
                    , $e->getMessage());
        }
    }

    /**
     * Creates Api for paypal
     *
     * @return Api
     */
    protected function createApi()
    {
        return new Api($this->apiConfig);
    }

    /**
     * Gets entrance fee model
     *
     * @return EntranceFee
     *
     * @throws LogicException
     */
    protected function getEntranceFee()
    {
        $fee = $this->repository->findEntranceFee();
        if (is_null($fee)) {
            $this->logger->alert('No fee configured');
            throw new PaymentMessage('No fee is configured');
        }

        return $fee;
    }

    /**
     * Gets current user
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    protected function getUser()
    {
        return $this->security->getToken()->getUser();
    }

}
