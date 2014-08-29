<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * GenderType is a form type for gender (only two, currently...)
 */
class GenderType extends AbstractType
{

    public function __construct(/* insert translation service here */)
    {
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'xy' => 'Male',
                'xx' => 'Female',
            )
        ));
    }

    public function getName()
    {
        return 'gender';
    }

    public function getParent()
    {
        return 'choice';
    }

}