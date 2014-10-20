<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * RepeatType is a form for Repeat entity
 */
class RepeatType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('original', 'text')
                ->add('repeat', 'submit');
    }

    public function getParent()
    {
        return 'social_publishing';
    }

    public function getName()
    {
        return 'social_repeat';
    }

}
