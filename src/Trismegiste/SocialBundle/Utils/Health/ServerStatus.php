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

    protected $networkInterf;

    public function __construct($ifName)
    {
        $this->networkInterf = $ifName;
    }

    /**
     * Gets CPU load average like top
     *
     * @return array
     */
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

    /**
     * Gets the the ratio between free disk space and total disk space
     *
     * @return float
     */
    public function getFreeSpaceRatio()
    {
        return disk_free_space('/') / disk_total_space('/');
    }

    /**
     * Gets monthly bandwidth with the help of vnstat
     *
     * @return array with 'tx' & 'rx' keys
     * 
     * @throws \RuntimeException
     */
    public function getMonthlyBandwidth()
    {
        $output = shell_exec('vnstat --json -i ' . $this->networkInterf);
        if (is_null($output)) {
            throw new \RuntimeException("vnstat not installed");
        }

        $stat = json_decode($output);
        $monthly = $stat->interfaces[0]->traffic->months;

        foreach ($monthly as $row) {
            if (($row->date->year == date('Y')) && ($row->date->month == date('n'))) {
                return ['rx' => $row->rx, 'tx' => $row->tx];
            }
        }

        return ['rx' => 0, 'tx' => 0];
    }

}
