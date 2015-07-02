<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\ExecutionContextInterface;
use Trismegiste\SocialBundle\Form\Oauth\AppKeyPairType;

/**
 * InstallParamType is a ...
 */
class InstallParamType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('facebook', new AppKeyPairType())
                ->add('twitter', new AppKeyPairType())
                ->add('github', new AppKeyPairType())
                ->add('Create', 'submit');
    }

    public function getName()
    {
        return 'install';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['constraints' => new Callback([
                'methods' => [function($data, ExecutionContextInterface $ctx) {
                        foreach ($data as $child) {
                            if (!is_null($child)) {
                                foreach ($child as $id)
                                    if (!empty($id)) {
                                        return;
                                    }
                            }
                        }
                        $ctx->addViolation('At least one of these providers must be filled');
                    }]
        ])]);
    }

}
