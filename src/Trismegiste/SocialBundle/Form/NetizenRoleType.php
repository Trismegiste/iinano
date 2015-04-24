<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * NetizenRoleType is a form for changing role of a Netizen
 */
class NetizenRoleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('group', 'role_choice')
                ->add('Promote', 'submit');
    }

    public function getName()
    {
        return 'social_netizen_role';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Trismegiste\SocialBundle\Security\Netizen'
        ]);
    }

}
