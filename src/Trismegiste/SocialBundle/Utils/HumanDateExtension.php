<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Utils;

use Mother;

/**
 * HumanDateExtension is a twig extension for a human date renderer filter
 */
class HumanDateExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('timeago', array($this, 'humanDateFilter'))
        ];
    }

    public function getName()
    {
        return 'humandate_extension';
    }

    public function humanDateFilter(\DateTime $pub)
    {
        $now = new \DateTime();

        $delta = $pub->diff($now);

        $mcb = ['y', 'm', 'd', 'h', 'i', 's'];
        $unit = ['year', 'month', 'day', 'hour', 'minute', 'second'];
        foreach ($mcb as $idx => $period) {
            if (0 < $curr = $delta->$period) {
                $word = $unit[$idx];

                if (($period == 'd') && ($curr >= 7)) {
                    $word = "week";
                    $curr /= 7;
                }

                $numberUnit = sprintf("%d %s%s", $curr, $word, ($curr > 1) ? 's' : '');
                $sentence = ($delta->invert === 0) ? "%s ago\n" : "in %s\n";

                return sprintf($sentence, $numberUnit);
            }
        }
    }

}