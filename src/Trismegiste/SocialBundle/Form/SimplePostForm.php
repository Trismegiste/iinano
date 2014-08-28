<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * SimplePostType is a form for SimplePost
 */
class SimplePostType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add("title")
                ->add('body','textarea')
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'simple_post';
    }

}