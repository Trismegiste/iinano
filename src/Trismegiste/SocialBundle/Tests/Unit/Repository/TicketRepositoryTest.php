<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\TicketRepository;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Ticket\Coupon;
use Trismegiste\SocialBundle\Ticket\EntranceFee;
use Trismegiste\SocialBundle\Ticket\Ticket;

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
                ->with(['-class' => 'coupon', 'hashKey' => 'found'])
                ->willReturn(new Coupon());

        $this->sut->findCouponByHash('found');
    }

    /**
     * @expectedException \Trismegiste\SocialBundle\Ticket\InvalidCouponException
     */
    public function testCreateTicketFromExpiredCoupon()
    {
        $coupon = new Coupon();
        $coupon->expiredAt = new \DateTime('yesterday');
        $this->assertFalse($coupon->isValid());

        $this->sut->createTicketFromCoupon($coupon);
    }

    /**
     * @expectedException \Trismegiste\SocialBundle\Ticket\InvalidCouponException
     */
    public function testCreateTicketFromOverusedCoupon()
    {
        $coupon = new Coupon();
        $coupon->expiredAt = new \DateTime('tomorrow');
        $coupon->incUse();
        $this->assertFalse($coupon->isValid());

        $this->sut->createTicketFromCoupon($coupon);
    }

    public function testCreateTicketFromValidCoupon()
    {
        $coupon = new Coupon();
        $coupon->expiredAt = new \DateTime('tomorrow');
        $this->assertTrue($coupon->isValid());

        $ticket = $this->sut->createTicketFromCoupon($coupon);
        $this->assertInstanceOf('Trismegiste\SocialBundle\Ticket\Ticket', $ticket);
        $this->assertFalse($coupon->isValid());
    }

    public function testUseValidCoupon()
    {
        $coupon = new Coupon();
        $coupon->expiredAt = new \DateTime('tomorrow');

        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'coupon', 'hashKey' => 'found'])
                ->willReturn($coupon);
        $this->repository->expects($this->exactly(2))
                ->method('persist');

        $this->user->expects($this->once())
                ->method('addTicket');

        $this->sut->useCouponFor('found');
        $this->assertFalse($coupon->isValid());
    }

    public function testCreateTicketFromPayment()
    {
        $fee = new EntranceFee();
        $fee->setAmount(1000);
        $fee->setCurrency('JPY');
        $fee->setDurationValue(12); // 12 months

        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'fee'])
                ->willReturn($fee);

        $ticket = $this->sut->createTicketFromPayment();
        $this->assertInstanceOf('Trismegiste\SocialBundle\Ticket\Ticket', $ticket);
        $this->assertTrue($ticket->isValid());
        $this->assertTrue($ticket->isValid(new \DateTime('+11 months')));
        $this->assertFalse($ticket->isValid(new \DateTime('+13 months')));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateTicketWithoutProperConfig()
    {
        $ticket = $this->sut->createTicketFromPayment();
    }

    public function testPersistNewPayment()
    {
        $fee = new EntranceFee();
        $fee->setDurationValue(12);
        $ticket = new Ticket($fee);

        $this->repository->expects($this->once())
                ->method('persist');
        $this->user->expects($this->once())
                ->method('addTicket');

        $this->sut->persistNewPayment($ticket);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteNotFoundCoupon()
    {
        $this->repository->expects($this->never())
                ->method('delete');

        $this->sut->deleteCoupon('5599f782e3f434f616787edc');
    }

    public function testDeleteCoupon()
    {
        $pk = '5599f782e3f434f616787edc';

        $coupon = new Coupon();
        $coupon->setId(new \MongoId($pk));
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($pk)
                ->willReturn($coupon);
        $this->repository->expects($this->once())
                ->method('delete')
                ->with($pk);

        $this->sut->deleteCoupon($pk);
    }

    public function getConversionExample()
    {
        return [
            [new \ArrayObject([1, 2]), new \ArrayObject([2, 3]), 0.5],
            [new \ArrayObject(), new \ArrayObject([2, 3]), 0],
            [new \ArrayObject([1, 2]), new \ArrayObject(), 1],
            [new \ArrayObject(), new \ArrayObject(), 0]
        ];
    }

    /**
     * @dataProvider getConversionExample
     */
    public function testGetConversionRate($converted, $nonconverted, $rate)
    {
        $this->repository->expects($this->at(0))
                ->method('getCursor')
                ->willReturn(new \ArrayObject($converted));
        $this->repository->expects($this->at(1))
                ->method('getCursor')
                ->willReturn(new \ArrayObject($nonconverted));

        $this->assertEquals($rate, $this->sut->getConversionRate());
    }

    public function getRenewalExample()
    {
        return [
            [new \ArrayObject(), new \ArrayObject(), 0],
            [new \ArrayObject([1, 2]), new \ArrayObject(), 1],
            [new \ArrayObject(), new \ArrayObject([2, 3]), 0],
            [new \ArrayObject([1]), new \ArrayObject([2, 3]), 1 / 3.0],
        ];
    }

    /**
     * @dataProvider getRenewalExample
     */
    public function testRenewalRate($rewed, $expired, $rate)
    {
        $this->repository->expects($this->at(0))
                ->method('getCursor')
                ->willReturn(new \ArrayObject($rewed));
        $this->repository->expects($this->at(1))
                ->method('getCursor')
                ->willReturn(new \ArrayObject($expired));

        $this->assertEquals($rate, $this->sut->getRenewalRate());
    }

    public function testFindFee()
    {
        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'fee']);
        $this->sut->findEntranceFee();
    }

    public function testPersistFee()
    {
        $fee = new EntranceFee();

        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'fee']);
        $this->repository->expects($this->once())
                ->method('persist')
                ->with($fee);

        $this->sut->persistEntranceFee($fee);
    }

    public function testPersistExistingFee()
    {
        $fee = new EntranceFee();
        $fee->setId(new \MongoId());

        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'fee'])
                ->willReturn($fee);
        $this->repository->expects($this->once())
                ->method('persist')
                ->with($fee);

        $this->sut->persistEntranceFee($fee);
    }

    /**
     * @expectedException \DomainException
     */
    public function testPersistDuplicateFee()
    {
        $fee = new EntranceFee();
        $db = clone $fee;
        $db->setId(new \MongoId());

        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'fee'])
                ->willReturn($db);
        $this->repository->expects($this->never())
                ->method('persist');

        $this->sut->persistEntranceFee($fee);
    }

}
