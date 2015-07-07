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

    public function __construct(SessionInterface $sess, TicketRepository $repo, LoggerInterface $logger)
    {
        $this->session = $sess;
        $this->repository = $repo;
        $this->logger = $logger;
    }

    public function setConfig(array $cfg)
    {
        $cfg['useraction'] = Api::USERACTION_COMMIT;
        $this->apiConfig = $cfg;
    }

    public function getUrlToGateway()
    {
        $api = $this->createApi();
        $fee = $this->getEntranceFee();

        try {
            $response = $api->setExpressCheckout([
                'PAYMENTREQUEST_0_AMT' => $fee->getAmount(),
                'PAYMENTREQUEST_0_CURRENCYCODE' => $fee->getCurrency(),
                'PAYMENTREQUEST_0_PAYMENTACTION' => Api::PAYMENTACTION_SALE,
                'NOSHIPPING' => Api::NOSHIPPING_NOT_DISPLAY_ADDRESS
            ]);
        } catch (HttpException $e) {
            $this->logger->critical('Paypal is not responding ' . $e->getMessage());
            throw new PaymentMessage('Unreachable payment gateway: ' . $e->getMessage());
        }

        if (!$response) {
            throw new PaymentMessage('Incorrect response from payment gateway');
        }
        if ($response['ACK'] !== Api::ACK_SUCCESS) {
            throw new PaymentMessage($response['L_ERRORCODE0']);
        }

        $token = $response['TOKEN'];
        $this->session->set(self::PAYPAL_TOKEN, $token);

        return $api->getAuthorizeTokenUrl($token);
    }

    protected function validateReturn(Request $request)
    {
        $currentUser = $request->getUser();
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

    public function processReturnFromGateway(Request $request)
    {
        $this->validateReturn($request);

        $currentUser = $request->getUser();
        $api = $this->createApi();

        $details = $api->getExpressCheckoutDetails([
            'TOKEN' => $token
        ]);

        if (Api::ACK_SUCCESS != $details['ACK'] ||
                empty($details['PAYERID']) ||
                ($details['PAYMENTREQUEST_0_AMT'] == 0)) {
            throw new PaymentMessage('Invalid details of payment');
        }

        if (Api::CHECKOUTSTATUS_PAYMENT_ACTION_NOT_INITIATED != $details['CHECKOUTSTATUS']) {
            throw new PaymentMessage("Payment already done");
        }

        $ticket = $this->repository->createTicketFromPayment();

        $response = $api->doExpressCheckoutPayment($details);
        if (Api::ACK_SUCCESS == $response['ACK']) {
            $ticket->setTransactionInfo([
                'transaction_id' => $response['TRANSACTIONID'],
                'payer_id' => $response['PAYERID'],
                'email' => $response['EMAIL']
            ]);
            // save payment
            try {
                $this->repository->persistNewPayment($ticket);
            } catch (\Exception $e) {
                $this->logger->error(sprintf('Payment was not saved for user %s transaction %s (reason: %s)')
                        , $currentUser->getUsername()
                        , $response['TRANSACTIONID']
                        , $e->getMessage());
            }
        }

        return $response['TRANSACTIONID'];
    }

    protected function createApi()
    {
        return new Api($this->apiConfig);
    }

    /**
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

}
