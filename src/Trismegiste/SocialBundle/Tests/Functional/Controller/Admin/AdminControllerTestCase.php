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

    protected $collection;

    protected function setUp()
    {
        parent::setUp();
        $this->collection = $this->getService('dokudoki.collection');
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();
        $this->assertCount(0, $this->collection->find());
        $this->addUserFixture('kirk');
        $this->addUserFixture('spock');

        $repo = $this->getService('social.netizen.repository');
        $user = $repo->findByNickname('kirk');
        $user->setGroup('ROLE_ADMIN');
        $repo->persist($user);
    }

    protected function assertSecuredPage($page)
    {
        $this->getPage($page);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->logIn('spock');
        $this->getPage($page);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        $this->logIn('kirk');
        $crawler = $this->getPage($page);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

}
