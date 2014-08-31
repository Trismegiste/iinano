<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * UniqueNickname is a validator to ensure uniqueness of a nickname for Author
 */
class UniqueNickname extends Constraint
{

    public $message = 'This nickname "%string%" is already used';

    public function validatedBy()
    {
        return 'unique_nickname';
    }

}