<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use \Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

/**
 * FollowerType is a choices list for follower of a Netizen
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

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $user = $this->security->getToken()->getUser();
        $builder->addModelTransformer(new UserTransformer($this->repository, $user->getFollowerIterator()));
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
