<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * CouponType is a form for Coupon entity
 */
class CouponType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('duration', 'integer')
                ->add('maximumUse', 'integer')
                ->add('Create', 'submit');
    }

    public function getName()
    {
        return 'free_coupon';
    }

}
