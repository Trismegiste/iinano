<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * AvatarType is a form for sending avatar
 */
class AvatarType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('picture', 'file')
                ->add('send', 'submit');
    }

    public function getName()
    {
        return 'avatar_type';
    }

}