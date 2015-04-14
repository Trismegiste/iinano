<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Config;

use Trismegiste\Yuurei\Persistence\Persistable;

/**
 * ParameterBag is a container for a set config parameters
 */
class ParameterBag implements Persistable
{

    use \Trismegiste\Yuurei\Persistence\PersistableImpl;

    public $data = [];

    public function __construct(array $content = [])
    {
        $this->data = $content;
    }

}
