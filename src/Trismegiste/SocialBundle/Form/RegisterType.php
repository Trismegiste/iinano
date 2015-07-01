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
use Trismegiste\SocialBundle\Security\NetizenFactory;
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

    public function __construct(NetizenFactory $repo, $regex)
    {
        $this->repository = $repo;
        $this->nicknameRegex = $regex;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
                        $builder->create('nickname', 'text', [
                            'constraints' => [
                                // all these constraints seem to me a little redundant but they give clear error messages
                                new NotBlank(),
                                new Length(['min' => 5, 'max' => 20]),
                                new UniqueNickname(),
                                new Regex(['pattern' => '#^' . $this->nicknameRegex . '$#', 'message' => "This nickname is not valid: only a-z, 0-9 & '-' characters are valid."])
                            ],
                            'mapped' => false,
                            'data' => $options['oauth_nickname'],
                            'attr' => ['placeholder' => 'Choose a nickname of 5 to 20 char. : a-z, 0-9 and \'-\'']
                        ])
                        ->addViewTransformer(new NicknameTransformer())
                )
                ->add('gender', 'gender', ['property_path' => 'profile.gender'])
                ->add('dateOfBirth', 'date', [
                    'property_path' => 'profile.dateOfBirth',
                    'years' => range(date('Y') - $options['minimumAge'], date('Y') - 100),
                    'empty_value' => 'Select',
                    'constraints' => new NotBlank()
                ])
                ->add('provider', 'hidden', ['mapped' => false, 'data' => $options['oauth_provider']])
                ->add('uid', 'hidden', ['mapped' => false, 'data' => $options['oauth_uid']])
                ->add('register', 'submit');
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
            $providerKey = $form->get('provider')->getData();
            $uid = $form->get('uid')->getData();

            return $form->isEmpty() && !$form->isRequired() ? null : $factory->create($nickname, $providerKey, $uid);
        };

        $resolver->setDefaults([
                    'empty_data' => $emptyData,
                    'data_class' => 'Trismegiste\SocialBundle\Security\Netizen'
                ])
                ->setRequired(['oauth_nickname', 'oauth_provider', 'oauth_uid', 'minimumAge']);
    }

}
