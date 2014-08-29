<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * RegisterType is a form to register an account
 */
class RegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nickname', 'text', ['constraints' => [
                        new NotBlank(),
                        new Length(['min' => 5, 'max' => 20])
            ]])
                ->add('password', 'password', ['constraints' => [
                        new NotBlank(),
                        new Length(['min' => 4, 'max' => 40])
            ]])
                ->add('fullName', 'text', ['constraints' => new NotBlank()])
                ->add('dateOfBirth', 'date')
                ->add('avatar', 'file')
                ->add('save', 'submit', ['attr' => ['class' => 'right']]);
    }

    public function getName()
    {
        return 'register';
    }

}