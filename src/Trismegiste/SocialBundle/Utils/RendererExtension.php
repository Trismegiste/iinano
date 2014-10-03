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
            new \Twig_SimpleFilter('gender', [$this, 'genderFilter'], ['needs_environment' => true])
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

    public function chooseTemplateFunction(Publishing $doc)
    {
        return sprintf($this->pathFormat, $this->templateAssoc[get_class($doc)]);
    }

    public function genderFilter(\Twig_Environment $env, $genderType)
    {
        // @todo is this the right way to do translation ?
        $trans = $env->getFilter('trans')->getCallable();

        switch ($genderType) {
            case 'xx':
                return call_user_func($trans, 'Female');
                break;
            case 'xy':
                return call_user_func($trans, 'Male');
                break;
            default: return '??';
        }
    }

}
