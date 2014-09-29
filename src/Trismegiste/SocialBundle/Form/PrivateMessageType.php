<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;
use Trismegiste\SocialBundle\Repository\PrivateMessageRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;

/**
 * PrivateMessageType is a form type for private message
 */
class PrivateMessageType extends AbstractType
{

    /** @var NetizenRepositoryInterface */
    protected $netizenRepo;

    /** @var PrivateMessageRepository */
    protected $pmRepo;

    public function __construct(PrivateMessageRepository $p, NetizenRepositoryInterface $n)
    {
        $this->pmRepo = $p;
        $this->netizenRepo = $n;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choice = [];
        $userListing = $this->netizenRepo->findBatchNickname($this->pmRepo->getTargetListing());
        foreach ($userListing as $user) {
            $choice[$user->getUsername()] = $user->getProfile()->fullName;
        }

        $builder->add('target', 'choice', ['choices' => $choice])
                ->add('message', 'textarea')
                ->add('send', 'submit');
    }

    public function getName()
    {
        return 'social_private_message';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $factory = $this->pmRepo;
        $resolver->setDefaults([
            'empty_data' => function(FormInterface $form, $data) use ($factory) {
                $target = $form->get('target')->getData();
                return $form->isEmpty() && !$form->isRequired() ? null : $factory->createNewMessageTo($data);
            },
            'data_class' => 'Trismegiste\Socialist\PrivateMessage'
        ]);
    }

}
