<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Utils;

/**
 * PerformanceBenchmark is a simple service for printing generation time and memory usage
 * in the footer of a page for example. Technically, it is not very accurate since the stopwatch
 * is initialized when the container is build so expects some 40-50 ms less than the true result
 * given by the (heavy) profiler which you can't decently activate in production
 *
 * Note: put these values in a comment html tag
 */
class PerformanceBenchmark
{

    protected $stopwatch;

    public function __construct()
    {
        $this->stopwatch = microtime(true);
    }

    /**
     * Get the duration
     *
     * @return int duration in milliseconds
     */
    public function getTimeDelay()
    {
        return sprintf('%.0f ms', 1000 * (microtime(true) - $this->stopwatch));
    }

    /**
     * Gets memory usage
     *
     * @return int size in octets
     */
    public function getMemoryUsage()
    {
        return memory_get_peak_usage();
    }

}
