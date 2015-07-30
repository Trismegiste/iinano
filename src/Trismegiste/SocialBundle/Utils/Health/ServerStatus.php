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

    /**
     * Gets the stats for memory
     * Works only on linux (who cares other platform ?)
     *
     * @return array
     */
    public function getMemoryLoad()
    {
        $data = explode("\n", file_get_contents("/proc/meminfo"));
        $meminfo = [];
        foreach ($data as $line) {
            if (preg_match('#^([^:]+):(.+)$#', $line, $match)) {
                $key = $match[1];
                $value = $match[2];
                if (preg_match('#^(\d+)\s+kB$#', trim($value), $match)) {
                    $value = $match[1] * 1024;
                }
                $meminfo[$key] = $value;
            }
        }

        return $meminfo;
    }

    public function getFreeSpaceRatio()
    {
        return disk_free_space('/') / disk_total_space('/');
    }

}
