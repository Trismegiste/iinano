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
        $link = $crawler->filter('.publishing')->selectLink('Like 0')->link();
        echo $link;
        $crawler = $this->client->click($link);
        $this->assertCount(1, $crawler->filter('.publishing')->selectLink('Unlike'));
        $this->assertEquals(1, (int) $crawler->filter('.publishing span.fan-count')->text());

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
        $link = $crawler->filter('.publishing')->selectLink('Like')->link();
        $crawler = $this->client->click($link);
        $this->assertCount(1, $crawler->filter('.publishing')->selectLink('Unlike'));
        $this->assertEquals(2, (int) $crawler->filter('.publishing span.fan-count')->text());

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
        $link = $crawler->filter('.publishing')->selectLink('Unlike')->link();
        $crawler = $this->client->click($link);
        $this->assertCount(1, $crawler->filter('.publishing')->selectLink('Like'));
        $this->assertEquals(1, (int) $crawler->filter('.publishing span.fan-count')->text());

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
        $link = $crawler->filter('.publishing .commentary')->selectLink('Like')->link();
        $crawler = $this->client->click($link);
        // check we have 'unlike' button
        $unlikeIter = $crawler->filter('.publishing .commentary')->selectLink('Unlike');
        $this->assertCount(1, $unlikeIter);
        //check the info counter
        $this->assertEquals(1, (int) $crawler->filter('.publishing .commentary span.fan-count')->text());
        // click on the unlike
        $crawler = $this->client->click($unlikeIter->link());
        $this->assertEquals(0, (int) $crawler->filter('.publishing .commentary span.fan-count')->text());
        // check the database
        $it = $repo->findLastEntries(0, 1);
        $it->rewind();
        $doc = $it->current();
        $comments = $doc->getCommentary();
        $this->assertCount(1, $comments);
        $this->assertEquals(0, $comments[0]->getFanCount());
    }

}
