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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'xy' => 'Male',
                'xx' => 'Female',
            ],
            'expanded' => true,
            'required' => true
        ]);
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