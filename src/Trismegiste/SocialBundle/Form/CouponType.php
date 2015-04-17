<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * CouponType is a form for Coupon entity
 */
class CouponType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('hashKey', 'text', [
                    'label' => 'Code',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 5])
                    ],
                    'attr' => ['placeholder' => 'case sensitive & minimum 5 characters']
                ])
                ->add('durationValue', 'integer', [
                    'label' => 'Duration (days)',
                    'data' => 5,
                    'constraints' => [
                        new NotBlank(),
                        new Range(['min' => 1, 'max' => 60])
                    ]
                ])
                ->add('maximumUse', 'integer', [
                    'data' => 1,
                    'constraints' => [
                        new NotBlank(),
                        new Range(['min' => 1, 'max' => 1000])
                    ]
                ])
                ->add('expiredAt', 'date', [
                    'data' => new \DateTime('+1 month'),
                    'years' => range(date('Y'), date('Y') + 2),
                ])
                ->add('Create', 'submit');
    }

    public function getName()
    {
        return 'free_coupon';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Trismegiste\SocialBundle\Ticket\Coupon']);
    }

}
