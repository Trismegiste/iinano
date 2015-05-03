<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository\MapReduce;

use MongoCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trismegiste\SocialBundle\Repository\MapReduce\CounterPerUser;
use Trismegiste\SocialBundle\Repository\MapReduce\RepeatCounter;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\SocialBundle\Security\Profile;
use Trismegiste\Socialist\Author;
use Trismegiste\Socialist\Commentary;
use Trismegiste\Socialist\SmallTalk;
use Trismegiste\Yuurei\Persistence\Repository;

/**
 * CounterPerUserTest tests CounterPerUser mru
 */
class CounterPerUserTest extends WebTestCase
{

    /** @var RepeatCounter */
    protected $sut;

    /** @var Repository */
    protected $repository;

    /** @var MongoCollection */
    protected $collection;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();
        $this->repository = $container->get('dokudoki.repository');
        $this->collection = $container->get('dokudoki.collection');
        $this->sut = new CounterPerUser($this->collection, 'test_report');
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();

        $authorList = [];
        foreach (['kirk', 'spock', 'mccoy'] as $nick) {
            $authorList[] = new Author($nick);
        }
        $target = $authorList[0];

        foreach ($authorList as $author) {
            $user = new Netizen($author);
            $user->setProfile(new Profile());
            $this->repository->persist($user);
            if ($target === $author) {
                $targetUser = $user;
            }
        }

        $source = new SmallTalk($target);
        $commentary = new Commentary($target);
        $source->attachCommentary($commentary);

        foreach ($authorList as $other) {
            if ($other === $target) {
                continue;
            }
            $source->addFan($other);
            $commentary->addFan($other);
        }
        $this->repository->batchPersist([$source, $source, $source]);

        $this->assertCount(6, $this->collection->find());

        return (string) $targetUser->getId();
    }

    /**
     * @depends initialize
     */
    public function testMapReduceUpdate($pk)
    {
        $this->sut->execute();

        $user = $this->repository->findByPk($pk);
        $this->assertEquals(3, $user->getProfile()->publishingCounter, "3 publish for kirk user");
        $this->assertEquals(3, $user->getProfile()->commentaryCounter, 'kirk user comments its own publish');
        $this->assertEquals(12, $user->getProfile()->likeCounter, "others like kirk's publish and kirk's comment");

        return $pk;
    }

}
