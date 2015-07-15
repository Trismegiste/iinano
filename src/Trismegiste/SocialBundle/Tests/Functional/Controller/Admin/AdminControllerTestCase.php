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

        $repo = $this->getService('social.netizen.repository');
        $users = ['simple' => 'ROLE_USER', 'admin' => 'ROLE_ADMIN', 'moderator' => 'ROLE_MODERATOR', 'manager' => 'ROLE_MANAGER'];
        foreach ($users as $nick => $group) {
            $this->addUserFixture($nick);
            $user = $repo->findByNickname($nick);
            $user->setGroup($group);
            $repo->persist($user);
        }

        $this->assertCount(count($users), $collection->find());
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
