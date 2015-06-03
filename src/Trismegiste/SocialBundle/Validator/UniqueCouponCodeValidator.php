<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Trismegiste\Yuurei\Persistence\RepositoryInterface;

/**
 * UniqueCouponCodeValidator is a for ensuring uniqueness of coupon hashkey
 */
class UniqueCouponCodeValidator extends ConstraintValidator
{

    protected $repository;

    public function __construct(RepositoryInterface $repo)
    {
        $this->repository = $repo;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($this->repository->findOne(['-class' => 'coupon', 'hashKey' => $value])) {
            $this->context->addViolation($constraint->message, ['%string%' => $value]);
        }
    }

}
