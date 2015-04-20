<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\TicketRepository;
use Trismegiste\Socialist\Author;

/**
 * TicketRepositoryTest tests TicketRepository
 */
class TicketRepositoryTest extends \PHPUnit_Framework_TestCase
{

    use \Trismegiste\SocialBundle\Tests\Helper\SecurityContextMock;

    /** @var TicketRepository */
    protected $sut;

    /** @var Trismegiste\Yuurei\Persistence\RepositoryInterface */
    protected $repository;

    /** @var Symfony\Component\Security\Core\SecurityContextInterface */
    protected $security;

    /** @var Trismegiste\Socialist\AuthorInterface */
    protected $author;

    /* @var \Trismegiste\SocialBundle\Security\Netizen */
    protected $user;

    protected function setUp()
    {
        $this->author = new Author('kirk');
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->security = $this->createSecurityContextMock($this->author);
        $this->user = $this->security->getToken()->getUser();

        $this->sut = new TicketRepository($this->repository, $this->security);
    }

    /**
     * @expectedException \Trismegiste\SocialBundle\Ticket\InvalidCouponException
     */
    public function testUseNotFoundCoupon()
    {
        $this->sut->useCouponFor('notfound');
    }

    public function testFindCouponByHash()
    {
        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['hashKey' => 'found'])
                ->willReturn(new \Trismegiste\SocialBundle\Ticket\Coupon());

        $this->sut->findCouponByHash('found');
    }

    /**
     * @expectedException \Trismegiste\SocialBundle\Ticket\InvalidCouponException
     */
    public function testCreateTicketFromExpiredCoupon()
    {
        $coupon = new \Trismegiste\SocialBundle\Ticket\Coupon();
        $coupon->expiredAt = new \DateTime('yesterday');
        $this->assertFalse($coupon->isValid());

        $this->sut->createTicketFromCoupon($coupon);
    }

    /**
     * @expectedException \Trismegiste\SocialBundle\Ticket\InvalidCouponException
     */
    public function testCreateTicketFromOverusedCoupon()
    {
        $coupon = new \Trismegiste\SocialBundle\Ticket\Coupon();
        $coupon->expiredAt = new \DateTime('tomorrow');
        $coupon->incUse();
        $this->assertFalse($coupon->isValid());

        $this->sut->createTicketFromCoupon($coupon);
    }

    public function testCreateTicketFromValidCoupon()
    {
        $coupon = new \Trismegiste\SocialBundle\Ticket\Coupon();
        $coupon->expiredAt = new \DateTime('tomorrow');
        $this->assertTrue($coupon->isValid());

        $ticket = $this->sut->createTicketFromCoupon($coupon);
        $this->assertInstanceOf('Trismegiste\SocialBundle\Ticket\Ticket', $ticket);
        $this->assertFalse($coupon->isValid());
    }

    public function testUseValidCoupon()
    {
        $coupon = new \Trismegiste\SocialBundle\Ticket\Coupon();
        $coupon->expiredAt = new \DateTime('tomorrow');

        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['hashKey' => 'found'])
                ->willReturn($coupon);
        $this->repository->expects($this->exactly(2))
                ->method('persist');

        $this->user->expects($this->once())
                ->method('addTicket');

        $this->sut->useCouponFor('found');
        $this->assertFalse($coupon->isValid());
    }

}
