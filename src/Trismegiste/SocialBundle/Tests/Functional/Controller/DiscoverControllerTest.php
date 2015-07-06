<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use MongoCollection;
use Trismegiste\SocialBundle\Repository\PublishingRepositoryInterface;
use Trismegiste\Socialist\SmallTalk;

/**
 * DiscoverControllerTest tests the DiscoverController
 */
class DiscoverControllerTest extends WebTestCasePlus
{

    /** @var MongoCollection */
    protected $collection;

    /** @var PublishingRepositoryInterface */
    protected $contentRepo;

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->logIn('kirk');
        $this->collection = $this->getService('dokudoki.collection');
        $this->contentRepo = $this->getService('social.publishing.repository');
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();
        $this->addUserFixture('kirk');
        $this->collection->ensureIndex(['message' => 'text', 'commentary.message' => 'text'], ['sparse' => true, 'weights' => ['message' => 3]]);
        $doc = new SmallTalk($this->createAuthor('kirk'));
        $doc->setMessage('and now something completely different');
        $this->contentRepo->persist($doc);
    }

    public function testShow()
    {
        $crawler = $this->getPage('discover_show');
        $this->assertCount(1, $crawler->filter('div.publishing:contains("different")'));
        $this->assertCount(1, $crawler->filter('nav.user:contains("kirk")'));
    }

    public function testNoDefaultContent()
    {
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'self']);
        $this->assertCount(0, $crawler->filter('div.widget:contains("No content here")'));
    }

    public function testDefaultContent()
    {
        // no friend then empty page then default content
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'friend']);
        $this->assertCount(1, $crawler->filter('div.widget:contains("No content here")'));
    }

}
