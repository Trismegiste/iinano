<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Payment;

use Symfony\Component\HttpFoundation\Request;

/**
 * Contract for a payment gateway
 */
interface Gateway
{

    public function getUrlToGateway();

    public function processReturnFromGateway(Request $request);

    public function setConfig(array $cfg);
}
