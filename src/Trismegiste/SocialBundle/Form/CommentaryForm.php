<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * CommentaryForm is a form for a Commentary
 */
class CommentaryForm extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add("message", 'textarea')
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'commentary';
    }

}