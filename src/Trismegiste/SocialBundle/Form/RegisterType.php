<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;
use Symfony\Component\Form\FormInterface;
use Trismegiste\SocialBundle\Validator\UniqueNickname;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * RegisterType is a form to register an account
 */
class RegisterType extends AbstractType
{

    protected $repository;

    public function __construct(NetizenRepositoryInterface $repo)
    {
        $this->repository = $repo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nickname', 'text', [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 5, 'max' => 20]),
                        new UniqueNickname(),
                        new Regex(['pattern'=>'#^[-\da-z]+$#'])
                    ],
                    'mapped' => false,
                    'attr' => ['placeholder' => 'Choose a nickname']
                ])
                ->add('password', 'password', [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 4, 'max' => 40])
                    ],
                    'mapped' => false
                ])
                // @todo don't put the avatar on registering (too many error), put it on profile_avatar_edit
                ->add('avatar', 'file', [
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new Image(['minWidth' => 300, 'minHeight' => 300, 'mimeTypes' => 'image/jpeg'])
                    ]
                ])
                ->add('profile', new ProfileType())
                ->add('save', 'submit', ['attr' => ['class' => 'right']]);
    }

    public function getName()
    {
        return 'netizen_register';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $factory = $this->repository;

        $emptyData = function (FormInterface $form, $data) use ($factory) {
                    $nickname = $form->get('nickname')->getData();
                    $password = $form->get('password')->getData();

                    return $form->isEmpty() && !$form->isRequired() ? null : $factory->create($nickname, $password);
                };

        $resolver->setDefaults([
            'empty_data' => $emptyData,
            'data_class' => 'Trismegiste\SocialBundle\Security\Netizen'
        ]);
    }

}