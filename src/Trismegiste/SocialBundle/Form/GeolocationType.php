<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * GeolocationType is a form 
 */
class GeolocationType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add("longitude", 'hidden')
                ->add('latitude', 'hidden');
    }

    public function getName()
    {
        return 'osm_location';
    }

}