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

    public function testDashboardSecure()
    {
        $this->getPage('admin_dashboard');
        $this->assertNotEquals(200, $this->client->getResponse()->getStatusCode());
    }

}
