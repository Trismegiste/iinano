<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Trismegiste\SocialBundle\Controller\AbuseReportController;
use Trismegiste\SocialBundle\Tests\Functional\Controller\WebTestCasePlus;
use Trismegiste\Socialist\SmallTalk;
use Trismegiste\Socialist\Author;

/**
 * AbuseReportControllerTest is a functional test for AbuseReportController
 */
class AbuseReportControllerTest extends WebTestCasePlus
{

    /**
     * @test
     */
    public function initialize()
    {
        $this->getService('dokudoki.collection')->drop();
        $this->addUserFixture('kirk');
        $this->addUserFixture('spock');
    }

    public function testLogBadRole()
    {
        $this->logIn('kirk');
        $this->getPage('abusive_listing');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testReportPublish()
    {
        $spock = $this->getService('social.netizen.repository')
                ->findByNickname('spock')
                ->getAuthor();
        $repo = $this->getService('social.content.repository');
        $repo->persist(new SmallTalk($spock));
        $this->logIn('kirk');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'all']);
        $this->assertCount(1, $crawler->filter('div.publishing'));
        $link = $crawler->filter('div.publishing')->selectLink('Report abuse or spam')->link();
        $this->client->click($link);
    }

    public function testLogWithModerator()
    {
        $repo = $this->getService('social.netizen.repository');
        $this->addUserFixture('moderat');
        $user = $repo->findByNickname('moderat');
        $user->setGroup('ROLE_MODERATOR');
        $repo->persist($user);

        $this->logIn('moderat');
        $crawler = $this->getPage('abusive_listing');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $lineSet = $crawler->filter('table.abuse-listing tr');
        $this->assertCount(2, $lineSet);
        $this->assertEquals('small', $lineSet->eq(1)->filter('td')->eq(1)->text());
        $this->assertEquals(1, (int) $lineSet->eq(1)->filter('td')->eq(3)->text());
    }

}
