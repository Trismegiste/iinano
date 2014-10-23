<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * VideoType is a form for Video
 */
class VideoType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', 'url', [
                    'constraints' => [
                        new NotBlank(),
                        new Url()
                    ]
                ])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'social_video';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['alias' => 'video']);
    }

    public function getParent()
    {
        return 'social_publishing';
    }

}
