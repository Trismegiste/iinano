<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * ChangePasswordType is a form type for changing user's password (edit only)
 */
class ChangePasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', 'repeated', [
                    'first_name' => 'password',
                    'second_name' => 'confirm_password',
                    'type' => 'password',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 4, 'max' => 40])
                    ]
                ])
                ->add('Change', 'submit');
    }

    public function getName()
    {
        return 'netizen_password';
    }

}
