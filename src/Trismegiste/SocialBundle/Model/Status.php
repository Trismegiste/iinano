<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Model;

use Trismegiste\Socialist\Publishing;

/**
 * Status is a status update with geolocation
 */
class Status extends Publishing
{

    public $longitude;
    public $latitude;
    public $elevation;
    public $message;

}