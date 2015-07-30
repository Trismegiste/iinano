<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Utils\Health;

/**
 * ServerStatus is a health monitoring for the server
 */
class ServerStatus
{

    public function getCpuLoad()
    {
        return sys_getloadavg();
    }

}
