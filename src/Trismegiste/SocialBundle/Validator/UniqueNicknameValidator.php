<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;

/**
 * UniqueNicknameValidator is he actual validator for ensuring uniqueness of nickname
 */
class UniqueNicknameValidator extends ConstraintValidator
{

    protected $repository;

    public function __construct(NetizenRepositoryInterface $repo)
    {
        $this->repository = $repo;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($this->repository->isExistingNickname($value)) {
            $this->context->addViolation($constraint->message, ['%string%' => $value]);
        }
    }

}