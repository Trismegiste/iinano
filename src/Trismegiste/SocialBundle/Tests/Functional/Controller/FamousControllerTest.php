<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Trismegiste\Socialist\Commentary;

/**
 * FamousControllerTest tests the FamousController
 */
class FamousControllerTest extends WebTestCasePlus
{

    protected $collection;
    protected $rootFqcn;

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->collection = $this->getService('dokudoki.collection');
        $this->rootFqcn = 'Trismegiste\Socialist\SmallTalk';
    }

    private function createPublishing($nick)
    {
        $refl = new \ReflectionClass($this->rootFqcn);
        return $refl->newInstance($this->createAuthor($nick));
    }

    protected function getSelfWallCrawlerFor($nick)
    {
        return $this->getPage('wall_index', ['wallNick' => $nick, 'wallFilter' => 'self']);
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
    public function testLikePublishing($pk)
    {
        $this->logIn('kirk');

        $crawler = $this->getSelfWallCrawlerFor('kirk');
        $link = $crawler->filter('.publishing article nav a[title=Like]')->link()->getUri();
        $crawler = $this->ajaxPost($link);
        $this->assertCount(1, $crawler->filter('a[title=Unlike]'));
        $this->assertEquals(1, (int) $crawler->filter('a[title=Unlike]')->text());

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
        $this->assertEquals(1, $restore->getFanCount());

        return $pk;
    }

    /**
     * @depends testLikePublishing
     */
    public function testLikePublishingWithOther($pk)
    {
        $this->logIn('spock');

        $crawler = $this->getSelfWallCrawlerFor('kirk');
        $link = $crawler->filter('.publishing article nav a[title=Like]')->link()->getUri();

        $crawler = $this->ajaxPost($link);
        $this->assertCount(1, $crawler->filter('a[title=Unlike]'));
        $this->assertEquals(2, (int) $crawler->filter('a[title=Unlike]')->text());

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
        $this->assertEquals(2, $restore->getFanCount());

        return $pk;
    }

    /**
     * @depends testLikePublishingWithOther
     */
    public function testUnlikePublishing($pk)
    {
        $this->logIn('kirk');

        $crawler = $this->getSelfWallCrawlerFor('kirk');
        $link = $crawler->filter('.publishing article nav a[title=Unlike]')->link()->getUri();

        $crawler = $this->ajaxPost($link);
        $this->assertCount(1, $crawler->filter('a[title=Like]'));
        $this->assertEquals(1, (int) $crawler->filter('a[title=Like]')->text());

        $restore = $this->getService('dokudoki.repository')->findByPk($pk);
        $this->assertInstanceOf($this->rootFqcn, $restore);
        $this->assertEquals(1, $restore->getFanCount());
    }

    public function testLikeCommentary()
    {
        $this->logIn('kirk');
        // add a commentary
        $repo = $this->getService('social.publishing.repository');
        $it = $repo->findLastEntries(0, 1);
        $it->rewind();
        $doc = $it->current();
        $doc->attachCommentary(new Commentary($this->createAuthor('spock')));
        $repo->persist($doc);
        // click on the 'like' on the commentary
        $crawler = $this->getSelfWallCrawlerFor('kirk');
        $link = $crawler->filter('.commentary article nav a[title=Like]')->link();
        $crawler = $this->ajaxPost($link->getUri());
        // check we have 'unlike' button
        $unlikeButton = $crawler->filter('a[title=Unlike]');
        $this->assertCount(1, $unlikeButton);
        //check the info counter
        $this->assertEquals(1, (int) $unlikeButton->text());

        // click on the unlike
        $crawler = $this->ajaxPost($unlikeButton->link()->getUri());
        $this->assertEquals(0, (int) $crawler->filter('a[title=Like]')->text());
        // check the database
        $it = $repo->findLastEntries(0, 1);
        $it->rewind();
        $doc = $it->current();
        $comments = iterator_to_array($doc->getCommentaryIterator());
        $this->assertCount(1, $comments);
        $this->assertEquals(0, $comments[0]->getFanCount());
    }

}
