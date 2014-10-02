<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * ProfileType is a type for Profile entity
 */
class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('gender', 'gender')
                ->add('fullName', 'text', ['constraints' => new NotBlank()])
                ->add('dateOfBirth', 'date', [
                    'years' => range(date('Y') - 100, date('Y') - 6),
                    'empty_value' => 'Select'
                ])
                ->add('Save', 'submit');
    }

    public function getName()
    {
        return 'profile';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Trismegiste\SocialBundle\Security\Profile']);
    }

}
