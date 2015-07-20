<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Trismegiste\Socialist\Publishing;

/**
 * PublishingControllerTestCase is test case for all entities managed by PublishingController
 */
abstract class PublishingControllerTestCase extends WebTestCasePlus
{

    protected $collection;
    protected $contentRepo;
    static protected $random;

    static public function setUpBeforeClass()
    {
        // each test case has a different random, avoid false positive
        static::$random = rand();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->logIn('kirk');
        $this->collection = $this->getService('dokudoki.collection');
        $this->contentRepo = $this->getService('social.publishing.repository');
    }

    abstract protected function getCreateLinkText();

    abstract protected function getFormNewContent();

    abstract protected function assertNewContent(Publishing $doc);

    abstract protected function getFormEditContent();

    abstract protected function assertEditContent(Publishing $doc);

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();
        $this->assertCount(0, $this->collection->find());
        $this->addUserFixture('kirk');
        $this->addUserFixture('spock');
    }

    public function testCreateFirstPost()
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->filter('div#menu')->selectLink($this->getCreateLinkText())->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, $this->getFormNewContent());

        $it = $this->contentRepo->findLastEntries();
        $this->assertCount(1, $it);
        $it->rewind();
        $doc = $it->current();

        $this->assertNewContent($doc);
        $this->assertEquals('kirk', $doc->getAuthor()->getNickname());

        return (string) $doc->getId();
    }

    /**
     * @depends testCreateFirstPost
     */
    public function testShow($pk)
    {
        $crawler = $this->getPage('pub_permalink', ['id' => $pk]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('#kirk#', $crawler->filter('.publishing article h4')->text());
    }

    /**
     * @depends testCreateFirstPost
     */
    public function testEdit($pk)
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->filter('div.publishing')->selectLink('Edit')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, $this->getFormEditContent());

        $this->assertCount(1, $this->contentRepo->findLastEntries());
        $doc = $this->contentRepo->findByPk($pk);

        $this->assertEditContent($doc);
        $this->assertEquals('kirk', $doc->getAuthor()->getNickname());
    }

    public function testCreateSecondPost()
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink($this->getCreateLinkText())->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, $this->getFormNewContent());

        $this->assertCount(2, $this->contentRepo->findLastEntries());
    }

    public function testCurrentlyEditedIsRemovedFromListing()
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->filter('div.publishing')->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $this->assertCount(1, $crawler->filter('div.publishing'));
    }

    public function testDeleteFirst()
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Delete');
        $hiddenFormId = $link->attr('data-social-delete');
        $form = $crawler->filter("form#$hiddenFormId")->form();
        $crawler = $this->client->click($form);

        $this->assertCount(1, $this->contentRepo->findLastEntries());
    }

    public function testNoEditFromOther()
    {
        //override user
        $this->logIn('spock');
        $crawler = $this->getPage('content_index');
        $this->assertCount(0, $crawler->filter('div.publishing')->selectLink('Edit'));
    }

    public function testHackEditFromOther()
    {
        //override user
        $this->logIn('spock');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'spock', 'wallFilter' => 'all']);
        $anchor = $crawler->filter("div.publishing a[id^=anchor]")
                        ->eq(0)->attr('id');
        preg_match('#^anchor-([\da-f]{24})$#', $anchor, $match);
        $pk = $match[1];

        // try to get the form edit
        $crawler = $this->getPage('publishing_edit', ['id' => $pk]);
        // we see the form...
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // ...but we cannot post
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());


        return $pk;
    }

    /**
     * @depends testHackEditFromOther
     */
    public function testHackDeleteFromOther($pk)
    {
        //override user
        $this->logIn('spock');
        // try to delete
        $crawler = $this->client->request('DELETE', $this->generateUrl('publishing_delete', ['id' => $pk]));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

}
