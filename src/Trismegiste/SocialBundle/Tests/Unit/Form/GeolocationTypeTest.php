<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\GeolocationType;

/**
 * GeolocationTypeTest tests GeolocationType
 */
class GeolocationTypeTest extends FormTestCase
{

    protected function createType()
    {
        return new GeolocationType();
    }

    public function getInvalidInputs()
    {
        return [
            [
                ['longitude' => null, 'latitude' => null, 'zoom' => null],
                ['longitude' => null, 'latitude' => null, 'zoom' => null]
            ],
            [
                ['longitude' => 200, 'latitude' => -100, 'zoom' => -3],
                ['longitude' => 200, 'latitude' => -100, 'zoom' => -3],
            ]
        ];
    }

    public function getValidInputs()
    {
        return [
            [
                ['longitude' => 7.3, 'latitude' => 43.7, 'zoom' => 15],
                ['longitude' => 7.3, 'latitude' => 43.7, 'zoom' => 15]
            ],
            [
                ['longitude' => 180, 'latitude' => 90, 'zoom' => 1],
                ['longitude' => 180, 'latitude' => 90, 'zoom' => 1]
            ]
        ];
    }

}
