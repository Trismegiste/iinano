<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Trismegiste\Socialist\Publishing;

/**
 * PublishingControllerSmallTalkTest tests tests PublishingController with SmallTalk
 */
class PublishingControllerSmallTalkTest extends PublishingControllerTestCase
{

    protected function assertEditContent(Publishing $doc)
    {
        $this->assertInstanceOf('Trismegiste\Socialist\SmallTalk', $doc);
        $this->assertEquals('Kon nichi wa ' . static::$random, $doc->getMessage());
    }

    protected function assertNewContent(Publishing $doc)
    {
        $this->assertInstanceOf('Trismegiste\Socialist\SmallTalk', $doc);
        $this->assertEquals('Hello from ' . static::$random, $doc->getMessage());
    }

    protected function getCreateLinkText()
    {
        return 'Message';
    }

    protected function getFormEditContent()
    {
        return ['social_small' => [
                'message' => 'Kon nichi wa ' . static::$random
        ]];
    }

    protected function getFormNewContent()
    {
        return ['social_small' => [
                'message' => 'Hello from ' . static::$random
        ]];
    }

}
