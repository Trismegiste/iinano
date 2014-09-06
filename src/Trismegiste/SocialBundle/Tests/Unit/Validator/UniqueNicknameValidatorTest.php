<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Validator;

use Trismegiste\SocialBundle\Validator\UniqueNicknameValidator;
use Trismegiste\SocialBundle\Validator\UniqueNickname;

/**
 * UniqueNicknameValidatorTest tests UniqueNicknameValidator
 */
class UniqueNicknameValidatorTest extends \PHPUnit_Framework_TestCase
{

    protected $context;
    protected $validator;
    protected $repository;

    protected function setUp()
    {
        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->repository = $this->getMock('Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface');
        $this->validator = new UniqueNicknameValidator($this->repository);
        $this->validator->initialize($this->context);
    }

    public function testUnique()
    {
        $this->context->expects($this->never())
                ->method('addViolation');

        $this->repository->expects($this->once())
                ->method('isExistingNickname')
                ->will($this->returnValue(false));

        $this->validator->validate('toto', new UniqueNickname());
    }

    public function testNonUnique()
    {
        $this->context->expects($this->once())
                ->method('addViolation');

        $this->repository->expects($this->once())
                ->method('isExistingNickname')
                ->will($this->returnValue(true));

        $this->validator->validate('toto', new UniqueNickname());
    }

    public function testAliased()
    {
        $constraint = new UniqueNickname();
        $this->assertFalse(class_exists($constraint->validatedBy()));
    }

}