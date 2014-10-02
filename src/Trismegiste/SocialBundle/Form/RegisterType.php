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
use Symfony\Component\Validator\Constraints\Regex;

/**
 * RegisterType is a form to register an account
 */
class RegisterType extends AbstractType
{

    protected $repository;
    protected $nicknameRegex;

    public function __construct(NetizenRepositoryInterface $repo, $regex)
    {
        $this->repository = $repo;
        $this->nicknameRegex = $regex;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nickname', 'text', [
                    'constraints' => [
                        // @todo all these constraints seem to me a little redundant
                        new NotBlank(),
                        new Length(['min' => 5, 'max' => 20]),
                        new UniqueNickname(),
                        new Regex(['pattern' => '#^' . $this->nicknameRegex . '$#'])
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
                ->add('gender', 'gender', ['property_path' => 'profile.gender'])
                ->add('fullName', 'text', [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 5, 'max' => 50])
                    ],
                    'property_path' => 'profile.fullName',
                    'attr' => ['placeholder' => 'Your full name (public)']
                ])
                ->add('dateOfBirth', 'date', [
                    'property_path' => 'profile.dateOfBirth',
                    'years' => range(date('Y') - 100, date('Y') - 6),
                    'empty_value' => 'Select'
                ])
                ->add('email', 'email', [
                    'attr' => ['placeholder' => "Optional : a valid email used only if you've lost your password (not public)"],
                    'property_path' => 'profile.email',
                    'required' => false
                ])
                ->add('register', 'submit', ['attr' => ['class' => 'right']]);
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
