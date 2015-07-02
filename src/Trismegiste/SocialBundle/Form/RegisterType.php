<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Trismegiste\OAuthBundle\Security\Token;
use Trismegiste\SocialBundle\Security\NetizenFactory;
use Trismegiste\SocialBundle\Security\NotRegisteredHandler;
use Trismegiste\SocialBundle\Validator\UniqueNickname;

/**
 * RegisterType is a form to register an account
 */
class RegisterType extends AbstractType
{

    protected $repository;
    protected $nicknameRegex;

    /** @var SessionInterface */
    protected $session;

    public function __construct(NetizenFactory $repo, $regex, SessionInterface $session)
    {
        $this->repository = $repo;
        $this->nicknameRegex = $regex;
        $this->session = $session;
    }

    protected function getSessionAttr($name)
    {
        return $this->session->get(NotRegisteredHandler::IDENTIFIED_TOKEN)->getAttribute($name);
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
                            'data' => $this->getSessionAttr('nickname'),
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
                ->add('register', 'submit');
    }

    public function getName()
    {
        return 'netizen_register';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $factory = $this->repository;
        $providerKey = $this->getSessionAttr(Token::PROVIDER_KEY_ATTR);
        $uid = $this->getSessionAttr(Token::UNIQUE_ID_ATTR);

        $emptyData = function (Options $options) use ($factory, $providerKey, $uid) {
            // is the form in admin mode ?
            $adminMode = $options['adminMode'];
            return function (FormInterface $form, $data) use ($factory, $providerKey, $uid, $adminMode) {
                $nickname = $form->get('nickname')->getData();

                // first view : all null
                if ($form->isEmpty() && !$form->isRequired()) {
                    return null;
                } else {
                    if ($adminMode) {
                        // create an admin
                        return $factory->createAdmin($nickname, $providerKey, $uid);
                    } else {
                        // create a simple user
                        return $factory->create($nickname, $providerKey, $uid);
                    }
                }
            };
        };

        $resolver->setDefaults([
                    'empty_data' => $emptyData,
                    'data_class' => 'Trismegiste\SocialBundle\Security\Netizen'
                ])
                ->setRequired(['minimumAge', 'adminMode']);
    }

}
