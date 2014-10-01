<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * GeolocationType is a form
 */
class GeolocationType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add("longitude", 'hidden', [
                    'constraints' => [
                        new NotBlank(),
                        new Range(['min' => -180, 'max' => 180])
                    ]
                ])
                ->add('latitude', 'hidden', [
                    'constraints' => [
                        new NotBlank(),
                        new Range(['min' => -90, 'max' => 90])
                    ]
                ])
                ->add('zoom', 'hidden', [
                    'constraints' => [
                        new NotBlank(),
                        new Range(['min' => 1, 'max' => 100])
                    ]
        ]);
    }

    public function getName()
    {
        return 'osm_location';
    }

}
