<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form\Picture;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

/**
 * LocalStorageType is the binary part of picture uploading to local storage
 */
class LocalStorageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('picture', 'file', [
                    'constraints' => [new Image()],
                    'attr' => ['accept' => 'image/*;capture=camera']
                ])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'social_picture_binarypart_localstorage';
    }

}
