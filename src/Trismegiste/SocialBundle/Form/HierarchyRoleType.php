<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $resolver->setDefaults([
            'choices' => array_combine(array_keys($this->hierarchyRole), array_keys($this->hierarchyRole)),
            'required' => true
        ]);
    }

}
