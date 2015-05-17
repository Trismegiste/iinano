<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * ChangePasswordType is a form type for changing user's password (edit only)
 */
class ChangePasswordType extends AbstractType
{

    public function getName()
    {
        return 'netizen_password';
    }

}
