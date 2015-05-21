<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\VideoType;

/**
 * VideoTypeTest tests the video form type
 */
class VideoTypeTest extends PublishingTestCase
{

    protected function createType()
    {
        return new VideoType();
    }

    protected function getModelFqcn()
    {
        return 'Trismegiste\Socialist\Video';
    }

    public function getInvalidInputs()
    {
        $validated = $this->createData();
        $validated->setUrl('fdf://malformed.url/');
        $post = [
            'url' => 'fdf://malformed.url/'
        ];
        return [
            [$post, $validated, ['url']]
        ];
    }

    public function getValidInputs()
    {
        $validated = $this->createData();
        $validated->setUrl('http://www.youtube.com/embed/sfd54sd546dsf');
        $post = [
            'url' => 'https://youtu.be/sfd54sd546dsf'
        ];
        return [
            [$post, $validated]
        ];
    }

}
