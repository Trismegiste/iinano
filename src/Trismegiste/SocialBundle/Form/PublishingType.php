<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Trismegiste\SocialBundle\Repository\NewPublishingRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * PublishingType is a "super-type" for all form type for Publishing subclasses
 */
class PublishingType extends AbstractType
{

    /** @var NewPublishingRepository */
    protected $repository;

    public function __construct(NewPublishingRepository $p)
    {
        $this->repository = $p;
    }

    public function getName()
    {
        return 'social_publishing';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('alias'));

        $factory = $this->repository;
        $emptyData = function (Options $options) use ($factory) {
            $classKey = $options['alias'];

            return function (FormInterface $form) use ($factory, $classKey) {
                return $form->isEmpty() && !$form->isRequired() ? null : $factory->create($classKey);
            };
        };

        $resolver->setDefaults([
            'empty_data' => $emptyData,
            'data_class' => 'Trismegiste\Socialist\Publishing'
        ]);
    }

    public function getParent()
    {
        return 'form';
    }

}
