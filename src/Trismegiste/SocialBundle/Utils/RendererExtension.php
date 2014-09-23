<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Utils;

/**
 * RendererExtension is a twig extension for a human renderer and
 * social renderer for published document
 */
class RendererExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('timeago', array($this, 'humanDateFilter'))
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('choose_template', function($doc) {
                        $map = [
                            'Trismegiste\Socialist\SimplePost' => 'simplepost_show',
                            'Trismegiste\Socialist\Status' => 'status_show'
                        ];

                        return 'TrismegisteSocialBundle:Content:' . $map[get_class($doc)] . '.html.twig';
                    })
                ];
            }

            public function getName()
            {
                return 'socialrenderer_extension';
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

                        $numberUnit = sprintf("%d %s%s", $curr, $word, ($curr >= 2) ? 's' : '');
                        $sentence = ($delta->invert === 0) ? "%s ago\n" : "in %s\n";

                        return sprintf($sentence, $numberUnit);
                    }
                }

                return 'now';
            }

        }
