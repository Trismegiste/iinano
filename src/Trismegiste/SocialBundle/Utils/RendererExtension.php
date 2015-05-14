<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Utils;

use Trismegiste\Socialist\Publishing;

/**
 * RendererExtension is a twig extension for a human renderer and
 * social renderer for published document
 */
class RendererExtension extends \Twig_Extension
{

    protected $pathFormat;
    protected $templateAssoc;
    protected $multiplier = [
        1 => 'k',
        'M',
        'G',
        'T'
    ];

    /**
     * Ctor
     *
     * @param string $path a formatted string for the show templates of Publishing subclasses (@see sprintf)
     * @param array $contentAlias an array of alias key => fqcn
     */
    public function __construct($path, array $contentAlias)
    {
        $this->pathFormat = $path;
        // first time in my php programer life I used this array function :
        $this->templateAssoc = array_flip($contentAlias);
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('timeago', [$this, 'humanDateFilter']),
            new \Twig_SimpleFilter('gender', [$this, 'genderFilter'], ['needs_environment' => true]),
            new \Twig_SimpleFilter('si', [$this, 'siFilter']),
            new \Twig_SimpleFilter('auto_url', [$this, 'autoUrlFilter'], [
                'pre_escape' => 'html',
                'is_safe' => array('html')])
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('choose_template', [$this, 'chooseTemplateFunction'])
        ];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'socialrenderer_extension';
    }

    public function humanDateFilter(/* \DateTime */ $pub)
    {
        // accepting all types but filtering only DateTime.
        // Twig is rather tolerant about variables and formatting
        // so I choose to keep consistency.
        if (!$pub instanceof \DateTime) {
            return $pub;
        }

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
                $sentence = ($delta->invert === 0) ? "%s ago" : "in %s";

                return sprintf($sentence, $numberUnit);
            }
        }

        return 'now';
    }

    public function chooseTemplateFunction(Publishing $doc)
    {
        return sprintf($this->pathFormat, $this->templateAssoc[get_class($doc)]);
    }

    public function genderFilter(\Twig_Environment $env, $genderType)
    {
        // @todo is this the right way to do translation ?
        // Is it better to do it in the twig itself with "|gender|trans()" ?
        $trans = $env->getFilter('trans')->getCallable();

        switch ($genderType) {
            case 'xx':
                return call_user_func($trans, 'Female');
            case 'xy':
                return call_user_func($trans, 'Male');
            default: return '?';
        }
    }

    /**
     * Render a value with multiplier : k,M,G,T...
     *
     * @param float|int $value
     *
     * @return string
     */
    public function siFilter($value)
    {
        if ($value < 1000) {
            return $value; // optim
        }

        $digit = log10($value);
        $power = floor($digit / 3);
        $afterComma = floor(fmod($digit, 3)) ? 0 : 1; // if [1k, 10k[ => one digit after comma else 0 digit

        return sprintf("%.{$afterComma}f%s", $value / pow(1000, $power), $this->multiplier[$power]);
    }

    /**
     * Parse a string a decorate URL with <a>
     *
     * @param string $value
     *
     * @return string
     */
    public function autoUrlFilter($value)
    {
        return preg_replace_callback('#(http\\://[^\s]+)#', function($match) {
            return '<a href="' . $match[1] . '">' . $match[1] . '</a>';
        }, $value);
    }

}
