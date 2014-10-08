<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

/**
 * PictureType is a form for Picture
 */
class PictureType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('storageKey', 'dokudoki_file')//, ['constraints' => [new Image()]])
                ->add('message', 'textarea', ['constraints' => [
                        new NotBlank(),
                        new Length(['min' => 3, 'max' => 140])
                    ]
                ])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'social_picture';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Trismegiste\Socialist\Picture']);
    }

}
