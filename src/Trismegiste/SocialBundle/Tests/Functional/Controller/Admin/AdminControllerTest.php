<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller\Admin;

use Trismegiste\SocialBundle\Tests\Functional\Controller\WebTestCasePlus;

/**
 * AdminControllerTest tests the admin dashboard
 */
class AdminControllerTest extends WebTestCasePlus
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

    public function testRedirectAccessWithGuest()
    {
        $this->getPage('admin_dashboard');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testDenyAccessWithSimpleUser()
    {
        $this->logIn('spock');
        $this->getPage('admin_dashboard');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDashboardAccessWithAdmin()
    {
        $this->logIn('kirk');
        $crawler = $this->getPage('admin_dashboard');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('div.dashboard-tile:contains("Users")'));
    }

}
