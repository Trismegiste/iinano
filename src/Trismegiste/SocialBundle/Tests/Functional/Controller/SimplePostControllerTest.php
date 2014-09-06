<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

/**
 * SimplePostControllerTest tests SimplePostController
 */
class SimplePostControllerTest extends WebTestCasePlus
{

    protected $collection;
    protected $contentRepo;

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->logIn('kirk');
        $this->collection = $this->getService('dokudoki.collection');
        $this->contentRepo = $this->getService('social.content.repository');
    }

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
        $link = $crawler->selectLink('Simple Post')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['simple_post' => ['title' => __CLASS__, 'body' => __METHOD__]]);

        $it = $this->contentRepo->findLastEntries();
        $this->assertCount(1, $it);
        $it->rewind();
        $doc = $it->current();

        $this->assertInstanceOf('Trismegiste\Socialist\SimplePost', $doc);
        $this->assertEquals(__CLASS__, $doc->getTitle());
        $this->assertEquals(__METHOD__, $doc->getBody());
        $this->assertEquals('kirk', $doc->getAuthor()->getNickname());

        return (string) $doc->getId();
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
        $this->client->submit($form, ['simple_post' => ['title' => __CLASS__, 'body' => __METHOD__]]);

        $this->assertCount(1, $this->contentRepo->findLastEntries());
        $doc = $this->contentRepo->findByPk($pk);

        $this->assertEquals(__METHOD__, $doc->getBody());
        $this->assertEquals('kirk', $doc->getAuthor()->getNickname());
    }

    public function testCreateSecondPost()
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Simple Post')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['simple_post' => ['title' => __CLASS__, 'body' => __METHOD__]]);

        $this->assertCount(2, $this->contentRepo->findLastEntries());
    }

    public function testDeleteFirst()
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Delete')->link();
        $crawler = $this->client->click($link);

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
        $crawler = $this->getPage('content_index');
        $anchor = $crawler->filter("div.publishing a[id^=anchor]")
                        ->eq(0)->attr('id');
        preg_match('#^anchor-([\da-f]{24})$#', $anchor, $match);
        $pk = $match[1];
        // try to get the form edit
        $crawler = $this->getPage('simplepost_edit', ['id' => $pk]);
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
        $crawler = $this->getPage('simplepost_delete', ['id' => $pk]);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

}