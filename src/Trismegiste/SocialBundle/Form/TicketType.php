<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * TicketType is a type for editing netizen's ticket
 */
class TicketType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('expiredAt', 'date')
                ->add('edit', 'submit');
    }

    public function getName()
    {
        return 'admin_ticket';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'class' => 'Trismegiste\SocialBundle\Ticket\Ticket'
        ]);
    }

}
