<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\SmallTalkType;

/**
 * SmallTalkTypeTest tests SmallTalkType
 */
class SmallTalkTypeTest extends PublishingTestCase
{

    protected function createType()
    {
        return new SmallTalkType();
    }

    protected function getModelFqcn()
    {
        return 'Trismegiste\Socialist\SmallTalk';
    }

    public function getInvalidInputs()
    {
        $validated = $this->createData();
        $validated->setMessage('gg');
        $post = [
            'message' => 'gg'
        ];
        return [
            [$post, $validated, ['message']]
        ];
    }

    public function getValidInputs()
    {
        $validated = $this->createData();
        $validated->setMessage('A small message above 10 chars');
        $post = [
            'message' => 'A small message above 10 chars'
        ];
        return [
            [$post, $validated]
        ];
    }

}
