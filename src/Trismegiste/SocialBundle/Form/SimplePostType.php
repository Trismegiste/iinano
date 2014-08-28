<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * SimplePostType is a form for SimplePost
 */
class SimplePostType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add("title", 'text', ['constraints' => [
                        new NotBlank(),
                        new Length(['min' => 3, 'max' => 80])
            ]])
                ->add('body', 'textarea', ['constraints' => [
                        new NotBlank(),
                        new Length(['min' => 10, 'max' => 280])
            ]])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'simple_post';
    }

}