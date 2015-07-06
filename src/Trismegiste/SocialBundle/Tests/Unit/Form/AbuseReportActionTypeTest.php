<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use stdClass;
use Trismegiste\SocialBundle\Form\AbuseReportActionType;

/**
 * AbuseReportActionTypeTest tests AbuseReportActionType
 */
class AbuseReportActionTypeTest extends FormTestCase
{

    protected $listing;

    protected function createType()
    {
        $this->listing = [
            'aaa' => $this->createEntry('aaa'),
            'bbb' => $this->createEntry('bbb')
        ];
        return new AbuseReportActionType(new \ArrayIterator($this->listing));
    }

    protected function createEntry($id)
    {
        $obj = new stdClass();
        $obj->name = $id;

        return $obj;
    }

    public function getInvalidInputs()
    {
        return [
            [
                [],
                ['selection_list' => [], 'action' => null],
                ['selection_list', 'action']
            ],
            [
                ['selection_list' => ['aaa']],
                ['selection_list' => [$this->createEntry('aaa')], 'action' => null],
                ['action']
            ],
            [
                ['action' => AbuseReportActionType::RESET],
                ['selection_list' => [], 'action' => AbuseReportActionType::RESET],
                ['selection_list']
            ],
            [
                ['selection_list' => ['zzz'], 'action' => AbuseReportActionType::DELETE],
                ['action' => AbuseReportActionType::DELETE],
                ['selection_list']
            ]
        ];
    }

    public function getValidInputs()
    {
        return [
            [
                ['selection_list' => ['aaa', 'bbb'], 'action' => AbuseReportActionType::DELETE],
                ['selection_list' => [$this->createEntry('aaa'), $this->createEntry('bbb')], 'action' => AbuseReportActionType::DELETE]
            ]
        ];
    }

}
