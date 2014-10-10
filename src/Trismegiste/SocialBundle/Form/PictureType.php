<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * PictureType is a form for Picture : contains all data except binaries
 */
class PictureType extends AbstractType
{

    /**
     * As you can see, this form cannot be valid unless there is some JS to fill
     * the hidden field
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', 'text', [
                    'required' => false,
                    'attr' => ['placeholder' => 'Optional: add a title on this picture'],
                    'constraints' => new Length(['max' => 80])
                ])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'social_picture';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['alias' => 'picture']);
    }

    public function getParent()
    {
        return 'social_publishing';
    }

}
