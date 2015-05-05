<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * NetizenFilterType is a filter for listing netizen in Admin
 */
class NetizenFilterType extends AbstractType
{

    protected $nicknameRegex;

    public function __construct($regex)
    {
        $this->nicknameRegex = $regex;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET')  // because of cache
                ->add('nickname', 'text', [
                    'constraints' => [
                        new Regex(['pattern' => '#^' . $this->nicknameRegex . '$#', 'message' => "This nickname is not valid: only a-z, 0-9 & '-' characters are valid."])
                    ],
                    'label' => 'Nickname start with',
                    'required' => false
                ])
                ->add('group', 'role_choice')
                ->add('Search', 'submit');
    }

    public function getName()
    {
        return 'social_netizen_filter';
    }

}
