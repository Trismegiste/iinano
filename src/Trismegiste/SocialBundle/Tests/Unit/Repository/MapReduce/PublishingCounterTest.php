<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository\MapReduce;

use Trismegiste\SocialBundle\Repository\MapReduce\PublishingCounter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trismegiste\Socialist\Repeat;
use Trismegiste\Socialist\SmallTalk;
use Trismegiste\Socialist\Author;

/**
 * PublishingCounterTest tests PublishingCounter
 */
class PublishingCounterTest extends WebTestCase
{

    /** @var RepeatCounter */
    protected $sut;

    /** @var \Trismegiste\Yuurei\Persistence\Repository */
    protected $repository;

    /** @var \MongoCollection */
    protected $collection;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();
        $this->repository = $container->get('dokudoki.repository');
        $this->collection = $container->get('dokudoki.collection');
        $this->sut = new RepeatCounter($this->collection, 'test_report');
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();

        $author = [];
        foreach (['kirk', 'spock', 'mccoy'] as $nick) {
            $author[] = new Author($nick);
        }

        $source = new SmallTalk($author[0]);
        $this->repository->persist($source);

        $rep[0] = new Repeat($author[1]);
        $rep[0]->setEmbedded($source);
        $this->repository->persist($rep[0]);

        $rep[1] = new Repeat($author[2]);
        $rep[1]->setEmbedded($rep[0]);
        $this->repository->persist($rep[1]);

        $this->assertCount(3, $this->collection->find());
    }

    public function testMapReduceUpdate()
    {
        $this->sut->execute();

        $listing = $this->repository->find();
        $this->assertCount(3, $listing);
        foreach ($listing as $doc) {
            // all publishing, including the source are updated :
            $this->assertEquals(2, $doc->getRepeatedCount(), get_class($doc));
        }
    }

}
