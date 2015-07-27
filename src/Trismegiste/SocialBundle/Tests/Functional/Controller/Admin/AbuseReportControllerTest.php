<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller\Admin;

use Trismegiste\Socialist\Author;
use Trismegiste\Socialist\Commentary;
use Trismegiste\Socialist\Picture;
use Trismegiste\Socialist\Repeat;
use Trismegiste\Socialist\SmallTalk;
use Trismegiste\Socialist\Status;
use Trismegiste\Socialist\Video;

/**
 * AbuseReportControllerTest tests AbuseReportController
 */
class AbuseReportControllerTest extends AdminControllerTestCase
{

    /**
     * @test
     */
    public function initialize()
    {
        parent::initialize();

        $author = [];
        foreach (['kirk', 'spock', 'mccoy'] as $nick) {
            $author[] = new Author($nick);
        }

        $doc0 = new SmallTalk($author[0]);
        $doc1 = clone $doc0;
        $doc0->report($author[1]);
        $doc2 = clone $doc0;
        $comm = new Commentary($author[1]);
        $comm->report($author[2]);
        $doc1->attachCommentary($comm);
        $doc3 = clone $doc1;
        $this->getService('dokudoki.repository')->batchPersist([$doc0, $doc1, $doc2, $doc3]);
    }

    public function testResetCounterOnPub()
    {
        $this->client->followRedirects();
        $this->logIn('moderator');
        $crawler = $this->getPage('admin_abusive_pub_listing');
        $this->assertCount(2, $crawler->filter('.content table input[type=checkbox]'));

        $check = $crawler->selectButton('Make it so')->form();
        $check['admin_abusereport_action[selection_list][0]']->tick();
        $check['admin_abusereport_action[action]'] = 'RESET';
        $crawler = $this->client->submit($check);
        $this->assertCount(1, $crawler->filter('.content table input[type=checkbox]'));
    }

    public function testDeleteOnPub()
    {
        $this->client->followRedirects();
        $this->logIn('moderator');
        $crawler = $this->getPage('admin_abusive_pub_listing');
        $this->assertCount(1, $crawler->filter('.content table input[type=checkbox]'));

        $check = $crawler->selectButton('Make it so')->form();
        $check['admin_abusereport_action[selection_list][0]']->tick();
        $check['admin_abusereport_action[action]'] = 'DELETE';
        $crawler = $this->client->submit($check);

        $this->assertCount(1, $crawler->filter('.content table th:contains("Reported")'));
        $this->assertCount(0, $crawler->filter('.content table input[type=checkbox]'));
    }

    public function testResetCounterOnComm()
    {
        $this->client->followRedirects();
        $this->logIn('moderator');
        $crawler = $this->getPage('admin_abusive_comm_listing');
        $this->assertCount(2, $crawler->filter('.content table input[type=checkbox]'));

        $check = $crawler->selectButton('Make it so')->form();
        $check['admin_abusereport_action[selection_list][0]']->tick();
        $check['admin_abusereport_action[action]'] = 'RESET';
        $crawler = $this->client->submit($check);
        $this->assertCount(1, $crawler->filter('.content table input[type=checkbox]'));
    }

    public function testDeleteOnComm()
    {
        $this->client->followRedirects();
        $this->logIn('moderator');
        $crawler = $this->getPage('admin_abusive_comm_listing');
        $this->assertCount(1, $crawler->filter('.content table input[type=checkbox]'));

        $check = $crawler->selectButton('Make it so')->form();
        $check['admin_abusereport_action[selection_list][0]']->tick();
        $check['admin_abusereport_action[action]'] = 'DELETE';
        $crawler = $this->client->submit($check);

        $this->assertCount(1, $crawler->filter('.content table th:contains("Reported")'));
        $this->assertCount(0, $crawler->filter('.content table input[type=checkbox]'));
    }

    public function testRenderListingWithAllTypes()
    {
        $repo = $this->getService('dokudoki.repository');
        $author = $this->createAuthor('elliot');
        $reporter = $this->createAuthor('e-corp');
        $doc = [
            new SmallTalk($author),
            new Picture($author),
            new Video($author),
            new Status($author),
            new Repeat($reporter)
        ];
        $doc[1]->setStorageKey('1a11ec0ffee.png');
        $doc[2]->setUrl('http://youtube.com/watch?v=fsociety');
        $doc[3]->setMessage('coney island');

        foreach ($doc as $item) {
            $item->report($reporter);
            $repo->persist($item);
        }
        $doc[4]->setEmbedded($doc[0]);
        $repo->persist($doc[4]); // the embedded need to have a pk

        $this->logIn('moderator');
        $crawler = $this->getPage('admin_abusive_pub_listing');
        $this->assertCount(5, $crawler->filter('.content table input[type=checkbox]'));
    }

}
