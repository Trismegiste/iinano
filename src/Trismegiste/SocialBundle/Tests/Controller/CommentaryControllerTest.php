<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

use Trismegiste\Socialist\SimplePost;
use Trismegiste\Socialist\Author;

/**
 * CommentaryControllerTest tests CommentaryController
 */
class CommentaryControllerTest extends WebTestCasePlus
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
        $post = new SimplePost(new Author('kirk'));
        $this->getService('dokudoki.repository')->persist($post);
        $this->assertCount(1, $this->collection->find());

        return (string) $post->getId();
    }

    /**
     * @depends initialize
     */
    public function testAddCommentary($pk)
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Reply')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['commentary' => ['message' => __METHOD__]]);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf('Trismegiste\Socialist\SimplePost', $restore);
        $comment = $restore->getCommentary();
        $this->assertCount(1, $comment);
        $comment = $comment[0];
        $this->assertEquals(__METHOD__, $comment->getMessage());
        $this->assertEquals('kirk', $comment->getAuthor()->getNickname());

        return $pk;
    }

    /**
     * @depends testAddCommentary
     */
    public function testEditCommentary($pk)
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->filter('div.commentary')->selectLink('Edit')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['commentary' => ['message' => __METHOD__]]);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf('Trismegiste\Socialist\SimplePost', $restore);
        $comment = $restore->getCommentary();
        $this->assertCount(1, $comment);
        $comment = $comment[0];
        $this->assertEquals(__METHOD__, $comment->getMessage());
        $this->assertEquals('kirk', $comment->getAuthor()->getNickname());

        return $pk;
    }

    /**
     * @depends testEditCommentary
     */
    public function testDeleteCommentary($pk)
    {
        $crawler = $this->getPage('content_index');
        $link = $crawler->filter('div.commentary')->selectLink('Delete')->link();
        $crawler = $this->client->click($link);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf('Trismegiste\Socialist\SimplePost', $restore);
        $comment = $restore->getCommentary();
        $this->assertCount(0, $comment);
    }

}