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

    public function deleteCoupon($id)
    {
        $obj = $this->repository->findByPk($id);
        if (!$obj instanceof Coupon) {
            throw new InvalidArgumentException(get_class($obj) . " is not a coupon");
        }

        $this->repository->delete($id);
    }

    /**
     * Finds (or not) the unique entrance fee
     *
     * @return EntranceFee|null
     */
    public function findEntranceFee()
    {
        return $this->repository->findOne([MapAlias::CLASS_KEY => 'fee']);
    }

    /**
     * Gets the conversion rate between users with expired coupon and
     * users who have converted their subscription with a paid ticket
     *
     * @return float a ratio between 0 to 1
     */
    public function getConversionRate()
    {
        $convertedCoupon = $this->repository->getCursor([
                    MapAlias::CLASS_KEY => 'netizen',
                    'ticket.0.purchase.-class' => 'fee',
                    'ticket.1.purchase.-class' => 'coupon'
                ])->count();

        $expiredCoupon = $convertedCoupon + $this->repository->getCursor([
                    MapAlias::CLASS_KEY => 'netizen',
                    'ticket.0.purchase.-class' => 'coupon',
                    'ticket.0.expiredAt' => ['$lte' => new \MongoDate()]
                ])->count();

        if ($expiredCoupon > 0) {
            return $convertedCoupon / (float) $expiredCoupon;
        }

        return 0; // bof...
    }

    /**
     * Gets the renewal rate between users with expired ticket and
     * users who have bought at least one new ticket
     *
     * @return float a ratio between 0 to 1
     */
    public function getRenewalRate()
    {
        $renewTicket = $this->repository->getCursor([
                    MapAlias::CLASS_KEY => 'netizen',
                    'ticket.0.purchase.-class' => 'fee',
                    'ticket.1.purchase.-class' => 'fee',
                    'ticket.0.expiredAt' => ['$gt' => new \MongoDate()]
                ])->count();

        $expiredFee = $this->repository->getCursor([
                    MapAlias::CLASS_KEY => 'netizen',
                    'ticket.0.purchase.-class' => 'fee',
                    'ticket.0.expiredAt' => ['$lte' => new \MongoDate()]
                ])->count();

        if (($expiredFee + $renewTicket) > 0) {
            return $renewTicket / (float) ($expiredFee + $renewTicket);
        }

        return 0; // bof...
    }

    /**
     * Persists the unique fee
     *
     * @param EntranceFee $fee
     */
    public function persistEntranceFee(EntranceFee $fee)
    {
        $dbFee = $this->findEntranceFee();

        if (is_null($dbFee) || ($dbFee->getId() === $fee->getId())) {
            $this->repository->persist($fee);
        } else {
            throw new \DomainException('Only one unique Fee must be configured');
        }
    }

}
