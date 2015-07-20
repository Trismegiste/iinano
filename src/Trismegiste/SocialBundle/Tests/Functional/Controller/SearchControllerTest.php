<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use MongoCollection;
use Trismegiste\SocialBundle\Repository\PublishingRepositoryInterface;
use Trismegiste\Socialist\SmallTalk;

/**
 * SearchControllerTest tests search pages
 */
class SearchControllerTest extends WebTestCasePlus
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

    public function testSearchPage()
    {
        $crawler = $this->getPage('search_listing', ['keyword' => 'nothing']);
        $this->assertStatusCode(200);
        $this->assertCount(0, $crawler->filter('div.publishing:contains("different")'));
    }

    public function testSearchPageWithResult()
    {
        $crawler = $this->getPage('search_listing', ['keyword' => 'something']);
        $this->assertCount(1, $crawler->filter('div.publishing:contains("different")'));
    }

    public function testAjaxMoreOnSearch()
    {
        $more = $this->generateUrl('ajax_search_more', ['keyword' => 'something', 'offset' => 0]);
        $crawler = $this->client->request('GET', $more, [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $this->assertCount(1, $crawler->filter('div.publishing:contains("different")'));
    }

}
