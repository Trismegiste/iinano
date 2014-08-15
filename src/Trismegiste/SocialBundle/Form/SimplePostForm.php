<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * SimplePostForm is a form for SimplePost
 */
class SimplePostForm extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add("title")
                ->add('body','textarea')
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'simple_form';
    }

}