<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

use Trismegiste\Socialist\SimplePost;
use Trismegiste\Socialist\Author;

/**
 * FamousControllerTest tests the FamousController
 */
class FamousControllerTest extends WebTestCasePlus
{

    protected $collection;

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->collection = $this->getService('dokudoki.collection');
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();
        $this->assertCount(0, $this->collection->find());
        $post = new SimplePost(new Author('kirk'));
        $this->getService('dokudoki.repository')->persist($post);
        $this->assertCount(1, $this->collection->find());

        return (string) $post->getId();
    }

    /**
     * @depends initialize
     */
    public function testLikeSimplePost($pk)
    {
        $this->logIn('kirk');

        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Like')->link();
        $crawler = $this->client->click($link);
        $this->assertCount(1, $crawler->selectLink('Unlike'));
        $this->assertEquals(1, (int) $crawler->filter('span.fan-count')->text());

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf('Trismegiste\Socialist\SimplePost', $restore);
        $this->assertEquals(1, $restore->getFanCount());

        return $pk;
    }

    /**
     * @depends testLikeSimplePost
     */
    public function testLikeSimplePostWithOther($pk)
    {
        $this->logIn('spock');

        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Like')->link();
        $crawler = $this->client->click($link);
        $this->assertCount(1, $crawler->selectLink('Unlike'));
        $this->assertEquals(2, (int) $crawler->filter('span.fan-count')->text());

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf('Trismegiste\Socialist\SimplePost', $restore);
        $this->assertEquals(2, $restore->getFanCount());

        return $pk;
    }

    /**
     * @depends testLikeSimplePostWithOther
     */
    public function testUnlikeSimplePost($pk)
    {
        $this->logIn('kirk');

        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Unlike')->link();
        $crawler = $this->client->click($link);
        $this->assertCount(1, $crawler->selectLink('Like'));
        $this->assertEquals(1, (int) $crawler->filter('span.fan-count')->text());

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf('Trismegiste\Socialist\SimplePost', $restore);
        $this->assertEquals(1, $restore->getFanCount());
    }

}