<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * DynamicCfgType is a form to edit preferences/config of this app
 */
class DynamicCfgType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('freeAccess', 'choice', [
                    'choices' => [false => 'No', true => 'Yes'],
                    'label' => 'Free access',
                    'expanded' => true,
                    'multiple' => false
                ])
                ->add('appTitle')
                ->add('minimumAge', 'integer', [
                    'constraints' => [
                        new GreaterThan(5)
                    ]
                ])
                ->add('Edit', 'submit');
    }

    public function getName()
    {
        return 'dynamic_config';
    }

}
