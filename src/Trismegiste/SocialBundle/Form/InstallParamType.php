<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * InstallParamType is a ...
 */
class InstallParamType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('facebook', new Oauth\AppKeyPairType())
                ->add('twitter', new Oauth\AppKeyPairType())
                ->add('github', new Oauth\AppKeyPairType())
                ->add('Create', 'submit');
    }

    public function getName()
    {
        return 'install';
    }

}
