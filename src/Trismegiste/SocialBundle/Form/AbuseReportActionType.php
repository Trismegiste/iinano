<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * AbuseReportActionType is a form for action choices in abuse report listing
 */
class AbuseReportActionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('action', 'choice', [
                    'empty_value'=>'---',
                    'choices' => ['reset report', 'delete content']
                ])
                ->add('makeItSo', 'submit');
    }

    public function getName()
    {
        return 'admin_abusereport_action';
    }

}
