<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Trismegiste\Socialist\Publishing;

/**
 * PublishingControllerPictureTest tests the PublishingController with Picture document
 */
class PublishingControllerPictureTest extends PublishingControllerTestCase
{

    protected function getCreateLinkText()
    {
        return 'Picture';
    }

    protected function getFormNewContent()
    {
        return [
            'social_picture' => [
                'picture' => new UploadedFile(__DIR__ . '/../../../Resources/public/img/mascot.png', 'fixture.png')
            ]
        ];
    }

    protected function assertNewContent(Publishing $doc)
    {
        $this->assertInstanceOf('Trismegiste\Socialist\Picture', $doc);
        $this->assertEquals('image/png', $doc->getMimeType());
        $this->assertRegExp('#^[\da-f]{40}\.png$#', $doc->getStorageKey());
    }

    protected function getFormEditContent()
    {
        return [
            'social_picture' => [
                'message' => 'Title from ' . static::$random,
            ]
        ];
    }

    protected function assertEditContent(Publishing $doc)
    {
        $this->assertEquals('Title from ' . static::$random, $doc->getMessage());
    }

}
