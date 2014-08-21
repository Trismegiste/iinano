<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

/**
 * SimplePostControllerTest tests SimplePostController
 */
class SimplePostControllerTest extends WebTestCasePlus
{

    protected $collection;

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->logIn('kirk');
        $this->collection = $this->getService('dokudoki.collection');
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();
        $this->assertCount(0, $this->collection->find());
    }

    public function testCreateFirstPost()
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Simple Post')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['simple_post' => ['title' => __CLASS__, 'body' => __METHOD__]]);

        $it = $this->collection->find();
        $this->assertCount(1, $it);
        $it->rewind();
        $doc = $it->current();

        $this->assertEquals('post', $doc['-class']);
        $this->assertEquals(__CLASS__, $doc['title']);
        $this->assertEquals(__METHOD__, $doc['body']);
        $this->assertEquals('kirk', $doc['author']['nickname']);

        return $doc['_id'];
    }

    /**
     * @depends testCreateFirstPost
     */
    public function testEdit($pk)
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['simple_post' => ['title' => __CLASS__, 'body' => __METHOD__]]);

        $this->assertCount(1, $this->collection->find());
        $doc = $this->collection->findOne(['_id' => $pk]);

        $this->assertEquals(__METHOD__, $doc['body']);
        $this->assertEquals('kirk', $doc['author']['nickname']);
    }

    public function testCreateSecondPost()
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Simple Post')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['simple_post' => ['title' => __CLASS__, 'body' => __METHOD__]]);

        $this->assertCount(2, $this->collection->find());
    }

    public function testDeleteFirst()
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Delete')->link();
        $crawler = $this->client->click($link);

        $this->assertCount(1, $this->collection->find());
    }

}