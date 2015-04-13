<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Config;

/**
 * ProviderInterface is a contract for read/write a config
 */
interface ProviderInterface
{

    public function write($obj);

    public function read();
}
