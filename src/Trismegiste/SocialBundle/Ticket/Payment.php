<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * Payment is a payment for acquiring Ticket
 */
class Payment extends AbstractPurchase implements Money
{

    use MoneyImpl;
}
