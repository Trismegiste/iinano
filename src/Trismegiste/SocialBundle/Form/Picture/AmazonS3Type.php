<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form\Picture;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

/**
 * AmazonS3Type is the binary part of picture uploading to Amazon S3
 */
class AmazonS3Type extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('picture', 'file', ['constraints' => [new Image()]])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'social_picture_binarypart_amazons3';
    }

}
