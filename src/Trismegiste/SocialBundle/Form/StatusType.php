<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;

/**
 * StatusType is a form for Status update (with embedded geolocation)
 */
class StatusType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add("location", new GeolocationType(), ['inherit_data' => true])
                ->add('message', 'text', ['constraints' => new Length(['min' => 3, 'max' => 80])])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'social_status';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['alias' => 'status']);
    }

    public function getParent()
    {
        return 'social_publishing';
    }

}
