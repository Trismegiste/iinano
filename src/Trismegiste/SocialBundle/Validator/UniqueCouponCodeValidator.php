<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Trismegiste\Yuurei\Persistence\RepositoryInterface;

/**
 * UniqueCouponCodeValidator is a validator for ensuring uniqueness of coupon hashkey
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
        /** @var $value Trismegiste\SocialBundle\Ticket\Coupon */
        $criterion = ['-class' => 'coupon', 'hashKey' => $value->hashKey];

        if ($value->getId() instanceof \MongoId) {
            $criterion['_id'] = ['$ne' => $value->getId()];
        }

        if ($this->repository->findOne($criterion)) {
            $this->context->addViolation($constraint->message, ['%string%' => $value->hashKey]);
        }
    }

}
