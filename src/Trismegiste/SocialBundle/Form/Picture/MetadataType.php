<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form\Picture;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * MetadataType is the metadata part of picture uploading
 */
class MetadataType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('storageKey', 'hidden')
                ->add('mimeType', 'hidden', [
                    'constraints' => new Regex(['pattern' => '#^image/(png|jpeg|jpg|gif)$#'])
                ])
                ->add('message', 'textarea', ['constraints' => [
                        new NotBlank(),
                        new Length(['min' => 3, 'max' => 140])
                    ]
                ])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'social_picture_metadata';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Trismegiste\Socialist\Picture']);
    }

}
