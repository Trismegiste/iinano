<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * VideoDataTransformer is a transformer from orignal video page url to embedded video url for iframe
 */
class VideoDataTransformer implements DataTransformerInterface
{

    protected $youtubeEmbed = "http://www.youtube.com/embed/%s";

    /**
     * From view to object
     *
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (is_object($value) && $value instanceof \Trismegiste\Socialist\Video) {
            $parts = parse_url($value->getUrl());
            switch ($parts['host']) {
                case 'www.youtube.com':
                case 'youtube.com':
                    if (!empty($parts['query'])) {
                        $param = [];
                        parse_str($parts['query'], $param);
                        if (isset($param['v'])) {
                            $value->setUrl(sprintf($this->youtubeEmbed, $param['v']));
                            // $value->setTemplateName('youtube')
                        }
                    }
                    break;
                case 'youtu.be':
                    if (preg_match('#^/([a-z0-9A-Z_-]+)$#', $parts['path'], $match)) {
                        $value->setUrl(sprintf($this->youtubeEmbed, $match[1]));
                        // $value->setTemplateName('youtube')
                    }
                    break;
            }
        }

        return $value;
    }

    public function transform($value)
    {
        return $value;
    }

}
