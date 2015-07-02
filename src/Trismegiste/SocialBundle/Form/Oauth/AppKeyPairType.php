<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form\Oauth;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * AppKeyPairType is a ...
 */
class AppKeyPairType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('client_id', 'text', ['required' => false])
                ->add('secret_id', 'text', ['required' => false])
                ->addModelTransformer(new KeyPairTransformer());
    }

    public function getName()
    {
        return 'app_key_pair';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['constraints' => new Callback([
                'methods' => [function($data, ExecutionContextInterface $ctx) {
                        if (empty($data['client_id']) xor empty($data['secret_id'])) {
                            $ctx->addViolationAt('[client_id]', 'All or neither of these two ID below must be blank');
                        }
                    }]
        ])]);
    }

}
