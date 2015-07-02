<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trismegiste\SocialBundle\Repository\AbuseReport;
use Trismegiste\Socialist\Author;
use Trismegiste\Socialist\SmallTalk;
use Trismegiste\Socialist\Commentary;
use Trismegiste\Socialist\Publishing;

/**
 * AbuseReportTest tests AbuseReport repository
 */
class AbuseReportTest extends WebTestCase
{

    /** @var AbuseReport */
    protected $sut;

    /** @var Symfony\Component\DependencyInjection\Container */
    protected $container;

    /** @var \Trismegiste\Yuurei\Persistence\RepositoryInterface */
    protected $repo;

    /** @var \MongoCollection */
    protected $coll;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->sut = $this->container->get('social.abusereport.repository');
        $this->coll = $this->container->get('dokudoki.collection');
        $this->repo = $this->container->get('dokudoki.repository');
    }

    public function getPublishing()
    {
        $author = [];
        foreach (['kirk', 'spock', 'mccoy'] as $nick) {
            $author[] = new Author($nick);
        }

        $doc0 = new SmallTalk($author[0]);
        $doc1 = clone $doc0;
        $doc1->report($author[1]);
        $doc2 = clone $doc1;
        $doc2->report($author[2]);
        $comm = new Commentary($author[1]);
        $comm->report($author[0]);
        $doc2->attachCommentary($comm);
        $doc3 = clone $doc0;
        $doc3->attachCommentary($comm);

        return [
            [$doc0, ['comm' => 0, 'pub' => 0, 'pubCount' => 0]],
            [$doc1, ['comm' => 0, 'pub' => 1, 'pubCount' => 1]],
            [$doc2, ['comm' => 1, 'pub' => 1, 'pubCount' => 2]],
            [$doc3, ['comm' => 1, 'pub' => 0, 'pubCount' => 0]]
        ];
    }

    /**
     * @dataProvider getPublishing
     */
    public function testCompileReport(Publishing $doc, array $assertion)
    {
        $this->coll->drop();
        $this->repo->persist($doc);

        $result = $this->sut->findMostReportedPublish();
        $this->assertCount($assertion['pub'], $result);
        if ($assertion['pub'] == 1) {
            $result->rewind();
            $pub = $result->current();
            $this->assertEquals($assertion['pubCount'], $pub['abusiveCount']);
        }

        $result = $this->sut->findMostReportedCommentary();
        $this->assertCount($assertion['comm'], $result);
        if ($assertion['comm'] == 1) {
            $result->rewind();
            $comm = $result->current()['commentary'];
            $this->assertEquals(1, $comm['abusiveCount']);
        }
    }

    protected function initDbWithOneReportedPublishing()
    {
        $author = [];
        foreach (['kirk', 'spock', 'mccoy'] as $nick) {
            $author[] = new Author($nick);
        }

        $doc0 = new SmallTalk($author[0]);
        $doc0->report($author[1]);
        $doc0->report($author[2]);

        $this->coll->drop();
        $this->repo->persist($doc0);
    }

    protected function initDbWithOnePublishingWithReportedComment()
    {
        $author = [];
        foreach (['kirk', 'spock', 'mccoy'] as $nick) {
            $author[] = new Author($nick);
        }

        $doc = new SmallTalk($author[0]);
        $comm = new Commentary($author[1]);
        $comm->report($author[2]);
        $comm->report($author[0]);
        $doc->attachCommentary($comm);

        $this->coll->drop();
        $this->repo->persist($doc);
    }

    public function testBatchResetPubReportedCount()
    {
        $this->initDbWithOneReportedPublishing();

        $result = $this->sut->findMostReportedPublish();
        $this->assertCount(1, $result);
        $reported = array_pop(iterator_to_array($result));
        $this->assertEquals(2, $reported['abusiveCount']);

        $this->sut->batchResetCounterPublish(iterator_to_array($result));
        $result = $this->sut->findMostReportedPublish();
        $this->assertCount(0, $result);
        $updated = $this->coll->findOne(['_id' => $reported['_id']]);
        $this->assertEquals(0, $updated['abusiveCount']);
    }

    public function testBatchDeleteReportedPub()
    {
        $this->initDbWithOneReportedPublishing();

        $result = $this->sut->findMostReportedPublish();
        $this->assertCount(1, $result);
        $reported = array_pop(iterator_to_array($result));
        $this->assertEquals(2, $reported['abusiveCount']);

        $this->sut->batchDeletePublish(iterator_to_array($result));
        $result = $this->sut->findMostReportedPublish();
        $this->assertCount(0, $result);
        $updated = $this->coll->findOne(['_id' => $reported['_id']]);
        $this->assertNull($updated);
    }

    public function testBatchResetCommentReportedCount()
    {
        $this->initDbWithOnePublishingWithReportedComment();

        $result = $this->sut->findMostReportedCommentary();
        $this->assertCount(1, $result);
        $reported = array_pop(iterator_to_array($result));
        $this->assertEquals(2, $reported['commentary']['abusiveCount']);

        $this->sut->batchResetCounterCommentary(iterator_to_array($result));
        $result = $this->sut->findMostReportedCommentary();
        $this->assertCount(0, $result);
        $updated = $this->coll->findOne(['_id' => $reported['_id']]);
        $this->assertEquals(0, $updated['commentary'][0]['abusiveCount']);
    }

    public function testBatchDeleteReportedComment()
    {
        $this->initDbWithOnePublishingWithReportedComment();

        $result = $this->sut->findMostReportedCommentary();
        $this->assertCount(1, $result);
        $reported = array_pop(iterator_to_array($result));
        $this->assertEquals(2, $reported['commentary']['abusiveCount']);

        $this->sut->batchDeleteCommentary(iterator_to_array($result));
        $result = $this->sut->findMostReportedCommentary();
        $this->assertCount(0, $result);
        $updated = $this->coll->findOne(['_id' => $reported['_id']]);
        $this->assertCount(0, $updated['commentary']);
    }

}
