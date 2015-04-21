<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller\Admin;

/**
 * FeeControllerTest tests the admin entrace fee edit
 */
class FeeControllerTest extends AdminControllerTestCase
{

    /**
     * @test
     */
    public function initialize()
    {
        parent::initialize();
    }

    public function testFeeEdit()
    {
        $this->assertSecuredPage('admin_entrancefee_edit');
        $crawler = $this->client->getCrawler();

        $form = $crawler->selectButton('Edit')->form();

        $crawler = $this->client->submit($form, ['entrance_fee' => [
                'amount' => '9.99',
                'currency' => 'EUR',
                'durationValue' => 12
        ]]);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNotNull($this->getService('dokudoki.repository')->findOne(['-class' => 'fee']));
    }

}
