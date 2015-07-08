<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * COnfig for paypal
 */
class Paypal extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', [
                    'constraints' => new NotBlank()
                ])
                ->add('password')
                ->add('signature')
                ->add('sandbox', 'choice', [
                    'choices' => [false => 'No', true => 'Yes'],
                    'label' => 'Sandbox',
                    'expanded' => true,
                    'multiple' => false,
                    'constraints' => [new NotBlank()]
        ]);
    }

    public function getName()
    {
        return 'paypal_config';
    }

}
