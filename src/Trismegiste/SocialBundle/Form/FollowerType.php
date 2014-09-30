<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * FollowerType is an autocomplete field for followers of logged netizen
 */
class FollowerType extends AbstractType
{

    /** @var NetizenRepositoryInterface */
    protected $repository;

    /** @var \Symfony\Component\Security\Core\SecurityContextInterface */
    protected $security;

    public function __construct(NetizenRepositoryInterface $repo, SecurityContextInterface $ctx)
    {
        $this->repository = $repo;
        $this->security = $ctx;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $user = $this->security->getToken()->getUser();
        $builder->addModelTransformer(new AuthorTransformer($this->repository, $user->getFollowerIterator()));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(['data_class' => 'Trismegiste\Socialist\Author']);
    }

    public function getName()
    {
        return 'social_follower_type';
    }

    public function getParent()
    {
        return 'text';
    }

}
