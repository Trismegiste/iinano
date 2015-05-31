<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Utils;

/**
 * PerformanceBenchmark is a ...
 */
class PerformanceBenchmark
{

    protected $stopwatch;

    public function __construct()
    {
        $this->stopwatch = microtime(true);
    }

    public function getTimeDelay()
    {
        return sprintf('%.0f', 1000 * (microtime(true) - $this->stopwatch));
    }

    public function getMemoryUsage()
    {
        return memory_get_peak_usage();
    }

}
