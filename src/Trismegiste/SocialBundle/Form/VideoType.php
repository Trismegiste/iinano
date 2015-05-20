<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Trismegiste\SocialBundle\Validator\YoutubeUrl;

/**
 * VideoType is a form for Video
 */
class VideoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', 'url', [
                    'constraints' => [
                        new NotBlank(),
                        new YoutubeUrl()
                    ],
                    'attr' => [
                        'data-form-focus' => null,
                        'placeholder' => 'Example: http://www.youtube.com/watch?v=azertyuiop'
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
