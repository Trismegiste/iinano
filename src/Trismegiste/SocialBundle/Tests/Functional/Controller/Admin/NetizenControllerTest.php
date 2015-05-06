<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller\Admin;

/**
 * NetizenControllerTest tests NetizenController
 */
class NetizenControllerTest extends AdminControllerTestCase
{

    /**
     * @test
     */
    public function initialize()
    {
        parent::initialize();
    }

    public function testAccessNetizenListing()
    {
        $this->assertSecuredPage('admin_netizen_listing');
        $crawler = $this->client->getCrawler();
        $this->assertCount(1, $crawler->filter('div.content tr th:contains("Nickname")'));
        $this->assertCount(0, $crawler->filter('div.content table tr td')); // no research criteria => no listing

        $form = $crawler->selectButton('Search')->form();
        $crawler = $this->client->submit($form, ['social_netizen_filter' =>
            ['nickname' => 'si']
        ]);

        $showLink = $crawler->filter('div.content table td a:contains("simple")');
        $this->assertCount(1, $showLink);

        return $showLink->first()->link();
    }

    /**
     * @depends testAccessNetizenListing
     */
    public function testNetizenShow(\Symfony\Component\DomCrawler\Link $showUrl)
    {
        $this->client->followRedirects();
        $this->logIn('admin');

        $crawler = $this->client->click($showUrl);
        $this->assertCount(1, $crawler->filter('div.content:contains("simple")'));
    }

}
