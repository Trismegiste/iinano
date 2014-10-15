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
                ])
                ->add('storageKey', 'hidden')
                ->add('mimeType', 'hidden')
                ->add('message', 'text', [
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
                $form->remove('storageKey');
                $form->remove('mimeType');
            }
        });

        $storage = $this->repository;
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($storage) {
            $form = $event->getForm();
            if ($form->has('picture')) {
                $picFile = $form->get('picture')->getViewData();
                $pub = $event->getData();
                $storage->store($pub, $picFile);
                $event->setData($pub);
            }
        });
    }

    public function getName()
    {
        return 'social_picture';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['alias' => 'picture']);
    }

    public function getParent()
    {
        return 'social_publishing';
    }

}
