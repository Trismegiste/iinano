<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

/**
 * HelpControllerTest tests help pages
 */
class HelpControllerTest extends WebTestCasePlus
{

    public function getHelpKey()
    {
        return [
            ['create_app_facebook'],
            ['create_app_twitter']
        ];
    }

    /**
     * @dataProvider getHelpKey
     */
    public function testShowHelp($key)
    {
        $this->getPage('social_help', ['id' => $key]);
        $this->assertEquals(200, $this->getCurrentResponse()->getStatusCode());
    }

}
