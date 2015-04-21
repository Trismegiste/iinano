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

    public function testSecurityDynamicConfig()
    {
        $this->assertSecuredPage('admin_dynamic_config_edit');
        $crawler = $this->client->getCrawler();
        $this->assertCount(1, $crawler->filter('div.content form:contains("Free access")'));
    }

    public function testEditDynamicConfig()
    {
        $this->client->followRedirects();
        $this->logIn('admin');

        $crawler = $this->getPage('admin_dynamic_config_edit');
        $form = $crawler->filter('div.content form')->form();
        $randString = 'AZERTY' . rand(); // to find some bug with cache
        $crawler = $this->client->submit($form, ['dynamic_config' => [
                'appTitle' => $randString,
                'minimumAge' => 13,
                'freeAccess' => 0
        ]]);
        sleep(3);
        $cfg = $this->getService('social.dynamic_config')->read();
        $this->assertEquals($randString, $cfg['appTitle']);
    }

}
