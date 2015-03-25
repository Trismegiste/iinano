<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * CouponType is a form for Coupon entity
 */
class CouponType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('hashKey')
                ->add('duration', 'integer', ['data' => 5])
                ->add('maximumUse', 'integer', ['data' => 1])
                ->add('expiredAt', 'date', ['data' => new \DateTime('tomorrow')])
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
