<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Trismegiste\SocialBundle\Repository\CommentaryFactory;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * CommentaryType is a form for a Commentary
 */
class CommentaryType extends AbstractType
{

    /** @var CommentaryFactory */
    protected $repository;

    public function __construct(CommentaryFactory $p)
    {
        $this->repository = $p;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("message", 'textarea', ['constraints' => [
                        new NotBlank(),
                        new Length(['min' => 3, 'max' => 280])
            ]])
                ->add('save', 'submit');
    }

    public function getName()
    {
        return 'social_commentary';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $factory = $this->repository;
        $emptyData = function (Options $options) use ($factory) {
            return function (FormInterface $form) use ($factory) {
                return $form->isEmpty() && !$form->isRequired() ? null : $factory->create();
            };
        };

        $resolver->setDefaults([
            'empty_data' => $emptyData,
            'data_class' => 'Trismegiste\Socialist\Commentary'
        ]);
    }

}
