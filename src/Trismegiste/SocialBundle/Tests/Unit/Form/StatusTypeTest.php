<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\StatusType;

/**
 * StatusTypeTest tests StatusType
 */
class StatusTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new StatusType();
    }

    protected function createData()
    {
        return new \Trismegiste\Socialist\Status(new \Trismegiste\Socialist\Author('kirk'));
    }

    public function getInvalidInputs()
    {
        $validated = $this->createData();
        $post = [
            'message' => '',
            'location' => ['longitude' => '', 'latitude' => '', 'zoom' => '']
        ];
        return [
            [$post, $validated]
        ];
    }

    public function getValidInputs()
    {
        $validated = $this->createData();
        $validated->setLatitude(43.7);
        $validated->setLongitude(7.3);
        $validated->setZoom(15);
        $validated->setMessage('Nissa la Bella');
        $post = [
            'message' => 'Nissa la Bella',
            'location' => ['longitude' => 7.3, 'latitude' => 43.7, 'zoom' => 15]
        ];
        return [
            [$post, $validated]
        ];
    }

}
