<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller\Admin;

use Trismegiste\SocialBundle\Tests\Functional\Controller\WebTestCasePlus;

/**
 * AdminControllerTestCase is a test case scenario & fixtures for testing admin pages
 */
class AdminControllerTestCase extends WebTestCasePlus
{

    protected function initialize()
    {
        $collection = $this->getService('dokudoki.collection');
        $collection->drop();
        $this->assertCount(0, $collection->find());

        $this->addUserFixture('simple');
        $this->addUserFixture('admin');

        $repo = $this->getService('social.netizen.repository');
        $user = $repo->findByNickname('admin');
        $user->setGroup('ROLE_ADMIN');
        $repo->persist($user);
        $this->assertCount(2, $collection->find());
    }

    protected function assertSecuredPage($page)
    {
        $this->getPage($page);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->logIn('simple');
        $this->getPage($page);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        $this->logIn('admin');
        $this->getPage($page);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

}
