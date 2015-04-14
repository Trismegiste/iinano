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

    /**
     * writes the parameters array somewhere
     *
     * @param array $param
     */
    public function write(array $param);

    /**
     * Reads the parameters array from somewhere
     *
     * @return array the dynamic config
     */
    public function read();
}
