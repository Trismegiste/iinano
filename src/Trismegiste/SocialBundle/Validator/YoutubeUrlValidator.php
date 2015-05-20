<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\UrlValidator;

/**
 * YoutubeUrlValidator is a constraint for Youtube url
 */
class YoutubeUrlValidator extends UrlValidator
{

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // first is this an url ?
        parent::validate($value, $constraint);
        // now parse it :
        $parts = parse_url($value);
        switch ($parts['host']) {
            case 'www.youtube.com':
            case 'youtube.com':
                if (!isset($parts['query']['v'])) {
                    $this->context->addViolation("This youtube page does not contain a video");
                }
                break;
            case 'youtu.be':
                if (!preg_match('#^/[a-z0-9A-Z_-]+$#', $parts['path'])) {
                    $this->context->addViolation("This youtube page does not contain a video");
                }
                break;
            default:
                $this->context->addViolation("This is not a youtube page");
        }
    }

}
