<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Validator;

use Symfony\Component\Validator\Constraints\Url;

/**
 * YoutubeUrl is a validator of a Youtube page. The format is either
 * - http(s)://www.youtube.com/watch?v=xxxxxxxxx
 * - http(s)://youtu.be/xxxxxxxxx
 */
class YoutubeUrl extends Url
{

    public function __construct()
    {

    }

}
