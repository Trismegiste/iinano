<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

/**
 * CommentaryControllerTest tests CommentaryController
 */
class CommentaryControllerTest extends WebTestCasePlus
{

    protected $collection;
    protected $wallParam;
    protected $rootFqcn;

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->logIn('kirk');
        $this->collection = $this->getService('dokudoki.collection');
        $this->wallParam = ['wallNick' => 'kirk', 'wallFilter' => 'self'];
        $this->rootFqcn = 'Trismegiste\Socialist\SimplePost';
    }

    private function createPublishing($nick)
    {
        $refl = new \ReflectionClass($this->rootFqcn);
        return $refl->newInstance($this->createAuthor($nick));
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();
        $this->assertCount(0, $this->collection->find());
        $post = $this->createPublishing('kirk');
        $this->getService('dokudoki.repository')->persist($post);
        $this->assertCount(1, $this->collection->find());
        $this->addUserFixture('kirk');
        $this->addUserFixture('spock');

        return (string) $post->getId();
    }

    /**
     * @depends initialize
     */
    public function testAddCommentary($pk)
    {
        $crawler = $this->getPage('wall_index', $this->wallParam);
        $link = $crawler->selectLink('Reply')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['commentary' => ['message' => __METHOD__]]);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
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
        $crawler = $this->getPage('wall_index', $this->wallParam);
        $link = $crawler->filter('div.commentary')->selectLink('Edit')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['commentary' => ['message' => __METHOD__]]);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
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
        $crawler = $this->getPage('wall_index', $this->wallParam);
        $link = $crawler->filter('div.commentary')->selectLink('Delete')->link();
        $crawler = $this->client->click($link);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
        $comment = $restore->getCommentary();
        $this->assertCount(0, $comment);

        return $pk;
    }

    /**
     * @depends testDeleteCommentary
     */
    public function testOtherAddCommentary($pk)
    {
        $this->login('spock');
        $crawler = $this->getPage('wall_index', $this->wallParam);
        $link = $crawler->filter('.publishing')->selectLink('Reply')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['commentary' => ['message' => __METHOD__]]);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
        $comment = $restore->getCommentary();
        $this->assertCount(1, $comment);
        $comment = $comment[0];
        $this->assertEquals(__METHOD__, $comment->getMessage());
        $this->assertEquals('spock', $comment->getAuthor()->getNickname());
    }

    public function testNoEditOnCommentaryFromOther()
    {
        $crawler = $this->getPage('wall_index', $this->wallParam);
        $this->assertCount(0, $crawler->filter('div.commentary')->selectLink('Edit'));
    }

    public function testNoDeleteOnCommentaryFromOther()
    {
        $crawler = $this->getPage('wall_index', $this->wallParam);
        $this->assertCount(0, $crawler->filter('div.commentary')->selectLink('Delete'));
    }

    public function testHackEditCommentaryFromOther()
    {
        $crawler = $this->getPage('wall_index', $this->wallParam);
        $this->assertCount(1, $crawler->filter('div.commentary:contains("spock")'));

        $anchor = $crawler->filter("div.commentary a[id^=anchor]")
                        ->eq(0)->attr('id');
        preg_match('#^anchor-([\da-f]{24})-([\da-f]{40})$#', $anchor, $match);
        $pk = array_merge(['id' => $match[1], 'uuid' => $match[2]], $this->wallParam);
        // try to get the form edit
        $crawler = $this->getPage('pub_commentary_edit', $pk);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        return $pk;
    }

    /**
     * @depends testHackEditCommentaryFromOther
     */
    public function testHackDeleteCommentaryFromOther($pk)
    {
        $crawler = $this->getPage('pub_commentary_delete', $pk);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

}
