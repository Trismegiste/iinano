<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * PrivateMessageType is a form type for private message
 */
class PrivateMessageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', 'textarea')
                ->add('send', 'submit');
    }

    public function getName()
    {
        return 'social_private_message';
    }

}
