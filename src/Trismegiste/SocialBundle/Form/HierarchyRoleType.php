<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * HierarchyRoleType is a choice list filled with hierarchy roles of security components
 */
class HierarchyRoleType extends AbstractType
{

    protected $hierarchyRole;

    public function __construct(array $roles)
    {
        $this->hierarchyRole = $roles;
    }

    public function getName()
    {
        return 'role_choice';
    }

    public function getParent()
    {
        return 'choice';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = [];
        foreach ($this->hierarchyRole as $key => $role) {
            $choices[$key] = ucfirst(strtolower(preg_replace('#^ROLE_#', '', $key)));
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'required' => true,
            'constraints' => new NotBlank()
        ]);
    }

}
