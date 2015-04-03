<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Trismegiste\SocialBundle\Ticket\Ticket;
use Trismegiste\SocialBundle\Ticket\Coupon;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\SocialBundle\Ticket\InvalidCouponException;

/**
 * TicketRepository is a repository for ticket coupon and fee
 */
class TicketRepository extends SecuredContentProvider
{

    public function __construct(RepositoryInterface $repo, SecurityContextInterface $ctx)
    {
        parent::__construct($repo, $ctx);
    }

    /**
     * Add a ticket created from a coupon to a user, persist a user and the coupon
     *
     * @param Netizen $user
     * @param Coupon $coupon
     */
    public function useCouponFor(Netizen $user, $couponHash)
    {
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
     * Finds a coupon from its hashkey
     *
     * @param string $hash
     *
     * @return Coupon
     */
    public function findCouponByHash($hash)
    {
        return $this->repository->findOne(['hashKey' => $hash]);
    }

    /**
     * Ticket factory
     *
     * @param Coupon $coupon
     * @throws InvalidCouponException
     */
    public function createTicketFromCoupon(Coupon $coupon)
    {
        if (!$coupon->isValid()) {
            throw new InvalidCouponException();
        }

        $ticket = new Ticket($coupon);
        $coupon->incUse();

        return $ticket;
    }

}
