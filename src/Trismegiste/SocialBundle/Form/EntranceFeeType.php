<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * EntranceFeeType is a form for the unique EntranceFee entity
 */
class EntranceFeeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('amount', 'money', [
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThan(['value' => 0])
                    ]
                ])
                ->add('currency', 'currency')
                ->add('duration', 'text', [
                    'constraints' => [
                        new NotBlank()
                    ]
                ])
                ->add('send', 'submit');
    }

    public function getName()
    {
        return 'entrance_fee';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([ 'data_class' => 'Trismegiste\SocialBundle\Ticket\EntranceFee']);
    }

}
