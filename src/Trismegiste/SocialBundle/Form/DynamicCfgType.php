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
        $builder->add('appTitle', 'text', [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 5, 'max' => 40])
                    ]
                ])
                ->add('subTitle', 'textarea', [
                    'required' => false,
                    'constraints' => [
                        new Length(['min' => 5, 'max' => 250])
                    ]
                ])
                ->add('minimumAge', 'integer', [
                    'constraints' => [
                        new Range(['min' => 6, 'max' => 21])
                    ]
                ])
                ->add('freeAccess', 'choice', [
                    'choices' => [false => 'No', true => 'Yes'],
                    'label' => 'Free access',
                    'expanded' => true,
                    'multiple' => false,
                    'constraints' => [new NotBlank()]
                ])
                ->add('google_tracking_id', 'text', ['required' => false])
                ->add('maintenanceMsg', 'textarea', [
                    'required' => false,
                    'label' => 'Maintenance message',
                    'attr'=>['placeholder' => 'This message will appear at the top of all pages. Clear it completly to hide it.'],
                    'constraints' => [
                        new Length(['min' => 5, 'max' => 250])
                    ]
                ])
                ->add('Save', 'submit');
    }

    public function getName()
    {
        return 'dynamic_config';
    }

}
