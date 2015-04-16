<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;

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
                ->add('appTitle', 'text', [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 5, 'max' => 40])
                    ]
                ])
                ->add('minimumAge', 'integer', [
                    'constraints' => [
                        new Range(['min' => 6, 'max' => 21])
                    ]
                ])
                ->add('Edit', 'submit');
    }

    public function getName()
    {
        return 'dynamic_config';
    }

}
