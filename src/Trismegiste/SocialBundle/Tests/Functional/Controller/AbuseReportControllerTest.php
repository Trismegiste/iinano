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
        $doc->setMessage('dummy message');
        $repo->persist($doc);
        $this->logIn('kirk');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'all']);
        $this->assertCount(1, $crawler->filter('div.publishing'));
        $link = $crawler->filter('div.publishing')->selectLink('Report abuse/spam')->link();
        $this->client->click($link);
        $this->assertStatusCode(302);
    }

    public function testReportedIsHidden()
    {
        $this->logIn('kirk');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'all']);
        $this->assertCount(1, $crawler->filter('div.publishing article.reported-content-panel'));
        $this->assertCount(0, $crawler->filter("article:contains('dummy message')"));
    }

    public function testReportCommentary()
    {
        $this->logIn('spock');
        /* @var $post SmallTalk  */
        $post = iterator_to_array($this->getService('social.publishing.repository')
                                ->findLastEntries(), false)[0];

        $repo = $this->getService('social.commentary.repository');
        $comm = $repo->create();
        $comm->setMessage('dummy comment');
        $repo->attachAndPersist($post, $comm);

        $this->logIn('kirk');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'all']);
        $this->assertCount(1, $crawler->filter(".commentary article:contains('dummy comment')"));

        $link = $crawler->filter('div.commentary')->selectLink('Report abuse/spam')->link();
        $this->client->click($link);
        $this->assertStatusCode(302);

        $crawler = $this->client->followRedirect();
        $this->assertCount(0, $crawler->filter(".commentary article:contains('dummy comment')"));
    }

    public function testLogBadRole()
    {
        $this->logIn('kirk');
        $this->getPage('admin_abusive_pub_listing');
        $this->assertStatusCode(403);
        $this->getPage('admin_abusive_comm_listing');
        $this->assertStatusCode(403);
    }

    public function testLogWithModeratorOnPublishListing()
    {
        $repo = $this->getService('social.netizen.repository');
        $this->addUserFixture('moderat');
        $user = $repo->findByNickname('moderat');
        $user->setGroup('ROLE_MODERATOR');
        $repo->persist($user);

        $this->logIn('moderat');
        $crawler = $this->getPage('admin_abusive_pub_listing');
        $this->assertStatusCode(200);
        $lineSet = $crawler->filter('table.abuse-listing tr');
        $this->assertCount(2, $lineSet);
        $this->assertCount(1, $lineSet->eq(1)->filter('td:contains("dummy message")'));
        $this->assertEquals(1, (int) $lineSet->eq(1)->filter('td')->eq(3)->text());
    }

    public function testLogWithModeratorOnCommentaryListing()
    {
        $this->logIn('moderat');
        $crawler = $this->getPage('admin_abusive_comm_listing');
        $this->assertStatusCode(200);
        $lineSet = $crawler->filter('table.abuse-listing tr');
        $this->assertCount(2, $lineSet);
        $this->assertCount(1, $lineSet->eq(1)->filter('td:contains("dummy comment")'));
        $this->assertEquals(1, (int) $lineSet->eq(1)->filter('td')->eq(3)->text());
    }

    public function testCancellingReportedOnPublishing()
    {
        $this->logIn('kirk');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'all']);
        $link = $crawler->filter('div.publishing article.reported-content-panel a')->link();
        $this->client->click($link);
        $crawler = $this->client->followRedirect();
        $this->assertCount(1, $crawler->filter("article:contains('dummy message')"));
    }

    public function testCancellingReportOnComentary()
    {
        $this->logIn('kirk');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'all']);
        $link = $crawler->filter('.commentary article.reported-content-panel a')->link();
        $this->client->click($link);
        $crawler = $this->client->followRedirect();
        $this->assertCount(1, $crawler->filter(".commentary article:contains('dummy comment')"));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     * @expectedExceptionMessage kekeke
     */
    public function testCommentNoOtherActionThanAddOrRemove()
    {
        $pk = '545e0475e3f4345d1e0097b8';
        $this->logIn('kirk');
        $this->generateUrl('pub_abusivereport', ['wallNick' => 'kirk', 'wallFilter' => 'self', 'id' => $pk, 'uuid' => $pk, 'action' => 'kekeke']);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     * @expectedExceptionMessage kekeke
     */
    public function testPubNoOtherActionThanAddOrRemove()
    {
        $pk = '545e0475e3f4345d1e0097b8';
        $this->logIn('kirk');
        $this->generateUrl('commentary_abusivereport', ['wallNick' => 'kirk', 'wallFilter' => 'self', 'id' => $pk, 'uuid' => $pk, 'action' => 'kekeke']);
    }

}
