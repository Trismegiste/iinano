<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * EntranceFeeType is a form for the unique EntranceFee entity
 */
class EntranceFeeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('amount', 'number', [
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThan(['value' => 0])
                    ],
                    'precision' => 2
                ])
                ->add('currency', 'currency', [
                    'preferred_choices' => ['USD', 'EUR', 'JPY'],
                    'empty_value' => ''
                ])
                ->add('durationValue', 'choice', [
                    'constraints' => [
                        new NotNull()
                    ],
                    'label' => 'duration',
                    'choices' => [
                        1 => 'one month',
                        3 => 'three months',
                        6 => 'six months',
                        12 => 'one year'
                    ],
                    'expanded' => true,
                    'multiple' => false
                ])
                ->add('Edit', 'submit');
    }

    public function getName()
    {
        return 'entrance_fee';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Trismegiste\SocialBundle\Ticket\EntranceFee']);
    }

}
