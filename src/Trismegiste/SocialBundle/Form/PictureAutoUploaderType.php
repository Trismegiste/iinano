<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * PictureAutoUploaderType is the binary part of picture uploading
 *
 */
class PictureAutoUploaderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('picture', 'file', [
            'constraints' => [new Image()],
            'attr' => ['accept' => 'image/*;capture=camera']
        ]);
    }

    public function getName()
    {
        return 'social_picture_autouploader';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['attr' => ['data-autouploader' => null]]);
    }

}
