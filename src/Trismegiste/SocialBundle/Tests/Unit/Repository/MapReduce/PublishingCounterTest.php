<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository\MapReduce;

use Trismegiste\SocialBundle\Repository\MapReduce\PublishingCounter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trismegiste\Socialist\SmallTalk;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Security\Netizen;

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
        $this->sut = new PublishingCounter($this->collection, 'test_report');
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();

        $author = new Author('kirk');
        $user = new Netizen($author);
        $user->setProfile(new \Trismegiste\SocialBundle\Security\Profile());
        $this->repository->persist($user);

        $source = new SmallTalk($author);
        $this->repository->batchPersist([$source, $source, $source]);

        $this->assertCount(4, $this->collection->find());

        return (string) $user->getId();
    }

    /**
     * @depends initialize
     */
    public function testMapReduceUpdate($userPk)
    {
        $this->sut->execute();

        $user = $this->repository->findByPk($userPk);
        $this->assertEquals(3, $user->getProfile()->publishingCounter);
    }

}
