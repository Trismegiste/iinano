<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\StatusType;

/**
 * StatusTypeTest tests StatusType
 */
class StatusTypeTest extends PublishingTestCase
{

    protected function createType()
    {
        return new StatusType();
    }

    protected function getModelFqcn()
    {
        return 'Trismegiste\Socialist\Status';
    }

    public function getInvalidInputs()
    {
        $validated = $this->createData();
        $validated->setMessage('gg');
        $post = [
            'message' => 'gg',
            'location' => ['longitude' => '', 'latitude' => '', 'zoom' => '']
        ];
        return [
            [$post, $validated, ['message']]
        ];
    }

    public function getValidInputs()
    {
        $validated = $this->createData();
        $validated->setLatitude('43');  // @todo force a float conversion in the setter of Status
        $validated->setLongitude('7');
        $validated->setZoom('15');
        $validated->setMessage('Nissa la Bella');
        $post = [
            'message' => 'Nissa la Bella',
            'location' => ['longitude' => 7, 'latitude' => 43, 'zoom' => 15]
        ];
        return [
            [$post, $validated]
        ];
    }

}
