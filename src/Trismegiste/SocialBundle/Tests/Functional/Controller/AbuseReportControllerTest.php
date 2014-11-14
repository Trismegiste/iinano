<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Trismegiste\SocialBundle\Tests\Functional\Controller\WebTestCasePlus;
use Trismegiste\Socialist\SmallTalk;

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

    public function testReportPublish()
    {
        $this->logIn('spock');
        $repo = $this->getService('social.publishing.repository');
        $doc = $repo->create('small');
        $repo->persist($doc);
        $this->logIn('kirk');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'all']);
        $this->assertCount(1, $crawler->filter('div.publishing'));
        $link = $crawler->filter('div.publishing')->selectLink('Report abuse/spam')->link();
        $this->client->click($link);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testReportCommentary()
    {
        $this->logIn('spock');
        /* @var $post SmallTalk  */
        $post = iterator_to_array($this->getService('social.publishing.repository')
                                ->findLastEntries(), false)[0];

        $repo = $this->getService('social.commentary.repository');
        $comm = $repo->create();
        $repo->attachAndPersist($post, $comm);

        $this->logIn('kirk');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'all']);
        $this->assertCount(1, $crawler->filter('div.commentary'));
        $link = $crawler->filter('div.commentary')->selectLink('Report abuse/spam')->link();
        $this->client->click($link);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testLogBadRole()
    {
        $this->logIn('kirk');
        $this->getPage('abusive_listing');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
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
        $this->assertCount(3, $lineSet);
        $this->assertEquals('small', $lineSet->eq(1)->filter('td')->eq(1)->text());
        $this->assertEquals(1, (int) $lineSet->eq(1)->filter('td')->eq(3)->text());
        $this->assertEquals('comm', $lineSet->eq(2)->filter('td')->eq(1)->text());
        $this->assertEquals(1, (int) $lineSet->eq(2)->filter('td')->eq(3)->text());
    }

}
