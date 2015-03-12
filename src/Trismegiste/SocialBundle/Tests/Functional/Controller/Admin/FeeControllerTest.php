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

    public function testFeeEdit()
    {
        $this->assertSecuredPage('entrancefee_edit');
        $crawler = $this->client->getCrawler();

        $form = $crawler->selectButton('Edit')->form();

        $crawler = $this->client->submit($form, ['entrance_fee' => [
                'amount' => '9.99',
                'currency' => 'EUR',
                'duration' => "+ 1 year"
        ]]);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNotNull($this->getService('dokudoki.repository')->findOne(['-class' => 'fee']));
    }

}
