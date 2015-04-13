<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Config;

use Trismegiste\Yuurei\Persistence\Persistable;

/**
 * Parameter is a collection of config parameters (cached)
 */
class Parameter implements Persistable
{

    use \Trismegiste\Yuurei\Persistence\PersistableImpl;

    public $freeAccess = false; // wether this app is free or not
    public $maintenanceMsg = ''; // global header message
    public $appTitle = 'iinano'; // title on the front login
    public $minimumAge = 18;      // minimum age for registering
    public $paypal;

}
