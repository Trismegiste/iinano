<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Payment;

use LogicException;
use Payum\Core\Exception\Http\HttpException;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Trismegiste\SocialBundle\Repository\TicketRepository;
use Trismegiste\SocialBundle\Ticket\EntranceFee;
use Trismegiste\Yuurei\Persistence\RepositoryInterface;

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

    public function __construct(SessionInterface $sess, TicketRepository $repo)
    {
        $this->session = $sess;
        $this->repository = $repo;
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
            throw new PaymentMessage('Unreachable payment gateway', 666, $e);
        }

        if (!$response) {
            throw new PaymentMessage('Unreachable payment gateway');
        }
        if ($response['ACK'] !== Api::ACK_SUCCESS) {
            throw new PaymentMessage($response['L_ERRORCODE0']);
        }

        $token = $response['TOKEN'];
        $this->session->set(self::PAYPAL_TOKEN, $token);

        return $api->getAuthorizeTokenUrl($token);
    }

    public function processReturnFromGateway(Request $request)
    {
        if (!$request->query->has('token')) {
            throw new PaymentMessage('Invalid return from payment gateway');
        }

        $token = $request->query->get('token');
        if ($token !== $this->session->get(self::PAYPAL_TOKEN)) {
            throw new PaymentMessage('Session has expired'); // or hacking
        }

        $api = $this->createApi();

        $details = $api->getExpressCheckoutDetails([
            'TOKEN' => $token
        ]);

        if (!($details['PAYERID'] &&
                Api::CHECKOUTSTATUS_PAYMENT_ACTION_NOT_INITIATED == $details['CHECKOUTSTATUS'] &&
                $details['PAYMENTREQUEST_0_AMT'] > 0)) {
            throw new PaymentMessage("Payment already done");
        }

        $this->
                $response = $api->doExpressCheckoutPayment($details);
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
            throw new PaymentMessage('No fee is configured');
        }

        return $fee;
    }

}
