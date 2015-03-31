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
    public function persistNewTicketFromCoupon(Netizen $user, Coupon $coupon)
    {
        $ticket = new Ticket($coupon);
        $user->addTicket($ticket);
        $coupon->incUse();

        $this->repository->persist($user);
        $this->repository->persist($coupon);
    }

    public function findCouponByHash($hash)
    {
        return $this->repository->findOne(['hashKey' => $hash]);
    }

}
