<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Config for paypal
 */
class PaypalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', [
                    'constraints' => new NotBlank()
                ])
                ->add('password', 'text', [
                    'constraints' => new NotBlank()
                ])
                ->add('signature', 'text', [
                    'constraints' => new NotBlank()
                ])
                ->add('sandbox', 'choice', [
                    'choices' => [false => 'No', true => 'Yes'],
                    'label' => 'Sandbox',
                    'expanded' => true,
                    'multiple' => false,
                    'constraints' => [new NotBlank()]
                ])
                ->add('Edit', 'submit');
    }

    public function getName()
    {
        return 'paypal_config';
    }

}
