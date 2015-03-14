<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller\Admin;

/**
 * AdminControllerTest tests the admin dashboard
 */
class AdminControllerTest extends AdminControllerTestCase
{

    /**
     * @test
     */
    public function initialize()
    {
        parent::initialize();
    }

    public function testDashboardAccessWithAdmin()
    {
        $this->assertSecuredPage('admin_dashboard');
        $crawler = $this->client->getCrawler();
        $this->assertCount(1, $crawler->filter('div.dashboard-tile:contains("Users")'));
    }

}
