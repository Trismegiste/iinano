<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * CommentaryType is a form for a Commentary
 */
class CommentaryType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add("message", 'textarea', ['constraints' => [
                        new NotBlank(),
                        new Length(['min' => 3, 'max' => 280])
            ]])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'commentary';
    }

}