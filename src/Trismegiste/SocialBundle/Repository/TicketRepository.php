<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use InvalidArgumentException;
use MongoCollection;
use RuntimeException;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\SocialBundle\Ticket\Coupon;
use Trismegiste\SocialBundle\Ticket\EntranceFee;
use Trismegiste\SocialBundle\Ticket\InvalidCouponException;
use Trismegiste\SocialBundle\Ticket\Ticket;

/**
 * TicketRepository is a repository for ticket coupon and fee
 */
class TicketRepository extends SecuredContentProvider
{

    /**
     * Add a ticket created from a coupon to a user, persist the user and the coupon
     *
     * @param string $couponHash
     *
     * @throws InvalidCouponException if coupon does not exists
     */
    public function useCouponFor($couponHash)
    {
        /** @var Netizen */
        $user = $this->security->getToken()->getUser(); // could not use getLoggedUser because not valid yet
        $coupon = $this->findCouponByHash($couponHash);
        if (is_null($coupon)) {
            throw new InvalidCouponException('The coupon does not exist');
        }

        $ticket = $this->createTicketFromCoupon($coupon);
        $user->addTicket($ticket);

        $this->repository->persist($user);
        $this->repository->persist($coupon);
    }

    /**
     * Find a coupon from its hashkey
     *
     * @param string $hash
     *
     * @return Coupon
     */
    public function findCouponByHash($hash)
    {
        return $this->repository->findOne([MapAlias::CLASS_KEY => 'coupon', 'hashKey' => $hash]);
    }

    /**
     * Ticket factory
     * WARNING: edge effect on Coupon
     *
     * @param Coupon $coupon (edge effect on usedCounter)
     *
     * @throws InvalidCouponException
     */
    public function createTicketFromCoupon(Coupon $coupon)
    {
        if (!$coupon->isValid()) {
            throw new InvalidCouponException("The coupon '{$coupon->getHashKey()}' has expired or has been used too many times");
        }

        $ticket = new Ticket($coupon);
        $coupon->incUse();

        return $ticket;
    }

    /**
     * Fetch the current config for EntranceFee and create a new ticket from it
     *
     * @return Ticket
     *
     * @throws RuntimeException if no fee has been configured
     */
    public function createTicketFromPayment()
    {
        /** @var EntranceFee */
        $fee = $this->findEntranceFee();
        if (is_null($fee)) {
            throw new RuntimeException('no payment has been configured');
        }

        return new Ticket($fee);
    }

    /**
     * Persits a new payment in the current user
     *
     * @param Ticket $ticket the new ticket
     */
    public function persistNewPayment(Ticket $ticket)
    {
        /** @var Netizen */
        $user = $this->security->getToken()->getUser();
        $user->addTicket($ticket);

        $this->repository->persist($user);
    }

    public function deleteCoupon($id, MongoCollection $coll)
    {
        $obj = $this->repository->findByPk($id);
        if (!$obj instanceof Coupon) {
            throw new InvalidArgumentException(get_class($obj) . " is not a coupon");
        }

        $coll->remove(['_id' => $obj->getId()]);
    }

    public function findEntranceFee()
    {
        return $this->repository->findOne([MapAlias::CLASS_KEY => 'fee']);
    }

}
