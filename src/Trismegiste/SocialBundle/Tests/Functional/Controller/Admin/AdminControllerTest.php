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

    public function testDashboardAccessWithAdmin()
    {
        $this->assertSecuredPage('admin_dashboard');
        $crawler = $this->client->getCrawler();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('div.dashboard-tile:contains("Users")'));
    }

}
