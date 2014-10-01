<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Trismegiste\Socialist\Publishing;

/**
 * StatusControllerTest tests tests PublishingController with Status
 */
class StatusControllerTest extends PublishingControllerTestCase
{

    protected function assertEditContent(Publishing $doc)
    {
        $this->assertInstanceOf('Trismegiste\Socialist\Status', $doc);
        $this->assertEquals(160, $doc->getLongitude());
        $this->assertEquals(35, $doc->getLatitude());
        $this->assertEquals('Kon nichi wa ' . static::$random, $doc->getMessage());
    }

    protected function assertNewContent(Publishing $doc)
    {
        $this->assertInstanceOf('Trismegiste\Socialist\Status', $doc);
        $this->assertEquals(7.2, $doc->getLongitude());
        $this->assertEquals(43.7, $doc->getLatitude());
        $this->assertEquals('Hello from ' . static::$random, $doc->getMessage());
    }

    protected function getCreateLinkText()
    {
        return 'Status';
    }

    protected function getFormEditContent()
    {
        return ['social_status' => [
                'location' => [
                    'longitude' => 160,
                    'latitude' => 35,
                    'zoom' => 8
                ],
                'message' => 'Kon nichi wa ' . static::$random
        ]];
    }

    protected function getFormNewContent()
    {
        return ['social_status' => [
                'location' => [
                    'longitude' => 7.2,
                    'latitude' => 43.7,
                    'zoom' => 8
                ],
                'message' => 'Hello from ' . static::$random
        ]];
    }

}
