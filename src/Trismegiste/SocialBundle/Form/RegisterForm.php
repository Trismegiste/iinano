<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * RegisterForm is a form to register an account
 */
class RegisterForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nickname')
                ->add('password', 'password')
                ->add('fullName')
                ->add('dateOfBirth', 'date')
                ->add('save', 'submit', ['attr' => ['class' => 'right']]);
    }

    public function getName()
    {
        return 'register';
    }

}