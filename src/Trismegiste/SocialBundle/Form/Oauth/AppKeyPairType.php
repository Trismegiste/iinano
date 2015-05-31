<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form\Oauth;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * AppKeyPairType is a ...
 */
class AppKeyPairType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('client_id')
                ->add('secret_id');
    }

    public function getName()
    {
        return 'app_key_pair';
    }

}
