<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Validator;

use Trismegiste\SocialBundle\Ticket\Coupon;
use Trismegiste\SocialBundle\Validator\UniqueCouponCode;
use Trismegiste\SocialBundle\Validator\UniqueCouponCodeValidator;

/**
 * UniqueCouponCodeValidatorTest tests UniqueCouponCodeValidator
 */
class UniqueCouponCodeValidatorTest extends \PHPUnit_Framework_TestCase
{

    protected $context;
    protected $validator;
    protected $repository;

    protected function setUp()
    {
        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->validator = new UniqueCouponCodeValidator($this->repository);
        $this->validator->initialize($this->context);
    }

    protected function createCoupon($key = 'AZERTY')
    {
        $c = new Coupon();
        $c->hashKey = $key;

        return $c;
    }

    public function testUnique()
    {
        $this->context->expects($this->never())
                ->method('addViolationAt');

        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'coupon', 'hashKey' => 'AZERTY'])
                ->willReturn(null);

        $this->validator->validate($this->createCoupon(), new UniqueCouponCode());
    }

    public function testNonUniqueOnCreation()
    {
        $this->context->expects($this->once())
                ->method('addViolationAt');

        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'coupon', 'hashKey' => 'AZERTY'])
                ->will($this->returnValue(true));

        $this->validator->validate($this->createCoupon(), new UniqueCouponCode());
    }

    public function testNonUniqueOnEdition()
    {
        $coupon = $this->createCoupon();
        $pk = new \MongoId();
        $coupon->setId($pk);

        $this->context->expects($this->once())
                ->method('addViolationAt');

        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'coupon', 'hashKey' => 'AZERTY', '_id' => ['$ne' => $pk]])
                ->will($this->returnValue(true));

        $this->validator->validate($coupon, new UniqueCouponCode());
    }

    public function testAliased()
    {
        $constraint = new UniqueCouponCode();
        $this->assertFalse(class_exists($constraint->validatedBy()));
    }

}
