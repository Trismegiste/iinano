<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Utils;

use Trismegiste\Yuurei\Persistence\Persistable;

/**
 * ConfigParameter is a collection of config parameters (cached)
 */
class ConfigParameter implements Persistable
{

    use \Trismegiste\Yuurei\Persistence\PersistableImpl;

    public $freeAccess = false; // wether this app is free or not
    public $maintenanceMsg = ''; // global header message
    public $appTitle = 'iinano'; // title on the front login
    public $minimumAge = 18;      // minimum age for registering
    public $paypal;

}
