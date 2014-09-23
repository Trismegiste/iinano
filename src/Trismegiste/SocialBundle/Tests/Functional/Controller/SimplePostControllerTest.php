<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Trismegiste\Socialist\Publishing;

/**
 * SimplePostControllerTest tests SimplePostController
 */
class SimplePostControllerTest extends PublishingControllerTestCase
{

    protected function getCreateLinkText()
    {
        return 'Simple Post';
    }

    protected function getFormNewContent()
    {
        return ['social_simplepost' => [
                'title' => 'A title ' . static::$random,
                'body' => 'A body with ' . static::$random
        ]];
    }

    protected function assertNewContent(Publishing $doc)
    {
        $this->assertInstanceOf('Trismegiste\Socialist\SimplePost', $doc);
        $this->assertEquals('A title ' . static::$random, $doc->getTitle());
        $this->assertEquals('A body with ' . static::$random, $doc->getBody());
    }

    protected function getFormEditContent()
    {
        return ['social_simplepost' => [
                'title' => 'A edited title ' . static::$random,
                'body' => 'A edited body with ' . static::$random
        ]];
    }

    protected function assertEditContent(Publishing $doc)
    {
        $this->assertInstanceOf('Trismegiste\Socialist\SimplePost', $doc);
        $this->assertEquals('A edited title ' . static::$random, $doc->getTitle());
        $this->assertEquals('A edited body with ' . static::$random, $doc->getBody());
    }

}
