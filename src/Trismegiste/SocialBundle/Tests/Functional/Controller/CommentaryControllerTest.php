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
        $this->rootFqcn = 'Trismegiste\Socialist\SmallTalk';
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
        $link = $crawler->filter('.publishing a[title=Reply]')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['social_commentary' => ['message' => __METHOD__]]);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
        $comment = iterator_to_array($restore->getCommentaryIterator());
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
        $this->client->submit($form, ['social_commentary' => ['message' => __METHOD__]]);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
        $comment = iterator_to_array($restore->getCommentaryIterator());
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
        $this->assertEquals(0, $restore->getCommentaryCount());

        return $pk;
    }

    /**
     * @depends testDeleteCommentary
     */
    public function testOtherAddCommentary($pk)
    {
        $this->login('spock');
        $crawler = $this->getPage('wall_index', $this->wallParam);
        $link = $crawler->filter('.publishing a[title=Reply]')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['social_commentary' => ['message' => __METHOD__]]);

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
        $comment = iterator_to_array($restore->getCommentaryIterator());
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
        // @todo we could change the anchor system for commentary by relying only on uuid
        // but the search for pk needs to be based on two anchors : publishing + commentary
        preg_match('#^anchor-([\da-f]{24})-([\da-f]{24})$#', $anchor, $match);
        $pk = array_merge(['id' => $match[1], 'uuid' => $match[2]], $this->wallParam);
        // try to get the form edit
        $crawler = $this->getPage('pub_commentary_edit', $pk);
        // we see the form...
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // ...but we cannot post
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['social_commentary' => ['message' => 'hacked']]);
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

    public function testAntiflood()
    {
        /* @var $repo \Trismegiste\SocialBundle\Repository\PublishingRepository */
        $repo = $this->getService('social.publishing.repository');
        $pub = $repo->create('small');
        $repo->persist($pub);
        $pk = (string) $pub->getId();

        $crawler = $this->getPage('wall_index', $this->wallParam);
        $this->assertCount(1, $crawler->filter("a[id='anchor-$pk']"));
        $link = $crawler->filter('.publishing a[title=Reply]')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['social_commentary' => ['message' => __METHOD__]]);

        $pub = $repo->findByPk($pk);
        $this->assertEquals(1, $pub->getCommentaryCount());

        // second reply :
        $link = $crawler->filter('.publishing a[title=Reply]')->link();
        $crawler = $this->client->click($link);
        $this->assertCount(0, $crawler->selectButton('Save'));
        $this->assertCount(1, $crawler->filter('script:contains("antiflood")'));
    }

}
