<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Iterator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * AbuseReportActionType is a form for action choices in abuse report listing
 */
class AbuseReportActionType extends AbstractType
{

    protected $listing;

    public function __construct(Iterator $listing)
    {
        $this->listing = $listing;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('selection_list', 'choice', [
                    'choice_list' => new AdminSelectionChoice($this->listing),
                    'expanded' => true,
                    'multiple' => true,
                    'constraints' => new NotBlank(['message' => 'Please select at least one item'])
                ])
                ->add('action', 'choice', [
                    'empty_value' => 'Select an action',
                    'choices' => [
                        'RESET' => 'Reset report counter',
                        'DELETE' => 'Delete content'
                    ]
                ])
                ->add('makeItSo', 'submit');
    }

    public function getName()
    {
        return 'admin_abusereport_action';
    }

}
