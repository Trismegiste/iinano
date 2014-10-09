<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * SmallTalkType is a form for SmallTalk
 */
class SmallTalkType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', 'textarea', ['constraints' => [
                        new NotBlank(),
                        new Length(['min' => 10, 'max' => 280])
            ]])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'social_small';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['alias' => 'small']);
    }

    public function getParent()
    {
        return 'social_publishing';
    }

}
