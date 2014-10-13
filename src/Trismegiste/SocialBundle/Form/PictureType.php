<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Trismegiste\SocialBundle\Repository\PictureRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * PictureType is a form for Picture
 */
class PictureType extends AbstractType
{

    /** @var PictureRepository */
    protected $repository;

    public function __construct(PictureRepository $p)
    {
        $this->repository = $p;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('picture', 'file', [
                    'constraints' => [new Image()],
                    'attr' => ['accept' => 'image/*;capture=camera'],
                    'label' => 'Picture',
                    'mapped' => false
                ])->add('message', 'text', [
                    'required' => false,
                    'attr' => ['placeholder' => 'Optional: add a title on this picture'],
                    'constraints' => new Length(['max' => 80])
                ])
                ->add('save', 'submit');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $pub = $event->getData();
            $form = $event->getForm();

            if ($pub && !is_null($pub->getId())) {
                $form->remove('picture');
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $form = $event->getForm();

            print_r($event->getData());
//            print_r($form->getData());
//            print_r($form->getExtraData());
            die();
        });
    }

    public function getName()
    {
        return 'social_picture';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $factory = $this->repository;
        $emptyData = function (Options $options) use ($factory) {

            return function (FormInterface $form) use ($factory) {
                if ($form->isEmpty() && !$form->isRequired()) {
                    return null;
                } else {
                    $picFile = $form->get('picture')->getData();
                    $pub = $factory->store($picFile);

                    return $pub;
                }
            };
        };

        $resolver->setDefaults([
            'empty_data' => $emptyData,
            'data_class' => 'Trismegiste\Socialist\Picture'
        ]);
    }

    public function getParent()
    {
        return 'form';
    }

}
