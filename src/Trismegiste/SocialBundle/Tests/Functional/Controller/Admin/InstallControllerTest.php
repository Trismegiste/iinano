<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller\Admin;

use Trismegiste\SocialBundle\Tests\Functional\Controller\WebTestCasePlus;

/**
 * InstallControllerTest tests the InstallController controller for first install
 */
class InstallControllerTest extends WebTestCasePlus
{

    public function testAccessIfNoUser()
    {
        $this->getService('dokudoki.collection')->drop();
        $crawler = $this->getPage('dynamic_config_create');
        $form = $crawler->selectButton('Create')->form();

        $this->client->submit($form, ['install' => [
                'facebook' => ['client_id' => 'aaa', 'secret_id' => 'bbb'],
                'twitter' => ['client_id' => 'ccc', 'secret_id' => 'ddd']
            ]
        ]);

        $config = $this->getService('dokudoki.repository')->findOne([]);
        $this->assertInstanceOf('Trismegiste\SocialBundle\Config\ParameterBag', $config);
    }

    public function testStillAccessEvenWithConfig()
    {
        $this->getPage('dynamic_config_create');
        $this->assertEquals(200, $this->getCurrentResponse()->getStatusCode());
    }

    public function testDeniedIfOneUser()
    {
        $this->addUserFixture('spock');

        $this->getPage('dynamic_config_create');
        $this->assertEquals(403, $this->getCurrentResponse()->getStatusCode());
    }

}
