<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\PublishingRepository;
use Trismegiste\Yuurei\Persistence\CollectionIterator;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Security\Netizen;

/**
 * PublishingRepositoryTest tests PublishingRepository
 */
class PublishingRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var PublishingRepository */
    protected $sut;
    protected $repository;
    protected $security;
    protected $author;
    protected $document;

    protected function setUp()
    {
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->sut = new PublishingRepository($this->repository, $this->security, ['message']);
        $this->author = new Author('kirk');
        $this->document = $this->getMockBuilder('Trismegiste\Socialist\Publishing')
                ->setConstructorArgs([$this->author])
                ->setMethods([])
                ->getMock();
    }

    public function testGetByPk()
    {
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123));

        $this->sut->findByPk(123);
    }

    public function testFindLastEntriesDefault()
    {
        $cursor = new CollectionIterator($this->getMock('MongoCursor', [], [], '', false), $this->repository);
        $this->repository->expects($this->once())
                ->method('find')
                ->with(['-class' => ['$in' => ['message']]])
                ->will($this->returnValue($cursor));

        $this->sut->findLastEntries();
    }

    public function testFindLastEntriesScan()
    {
        $cursor = new CollectionIterator($this->getMock('MongoCursor', [], [], '', false), $this->repository);
        $this->repository->expects($this->once())
                ->method('find')
                ->with(['-class' => ['$in' => ['message']], 'owner.nickname' => ['$in' => ['kirk']]])
                ->will($this->returnValue($cursor));

        $this->sut->findLastEntries(0, 20, new \ArrayIterator([$this->author]));
    }

    public function testPersist()
    {
        $this->repository->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($this->document));

        $this->sut->persist($this->document);
    }

    private function createNetizen($nick)
    {
        $mock = $this->getMock("Trismegiste\Socialist\AuthorInterface");
        $mock->expects($this->any())
                ->method('getNickname')
                ->will($this->returnValue($nick));

        $user = new Netizen($mock);
        $user->setId(new \MongoId());

        return $user;
    }

    public function createGraph()
    {
        $user = [];
        foreach (['kirk', 'scotty', 'mccoy', 'spock', 'sulu'] as $nick) {
            $user[] = $this->createNetizen($nick);
        }

        $user[1]->follow($user[0]);
        $user[2]->follow($user[0]);
        $user[4]->follow($user[0]);
        $user[0]->follow($user[2]);
        $user[0]->follow($user[3]);

        return [
            [$user[0], 'self', ['kirk']],
            [$user[0], 'following', ['mccoy', 'spock']],
            [$user[0], 'follower', ['scotty', 'mccoy', 'sulu']],
            [$user[0], 'friend', ['mccoy']]
        ];
    }

    /**
     * @dataProvider createGraph
     */
    public function testFindWallEntriesWithAuthor($user, $wallFilter, $authorName)
    {
        $cursor = new CollectionIterator($this->getMock('MongoCursor', [], [], '', false), $this->repository);
        $this->repository->expects($this->once())
                ->method('find')
                ->with(['-class' => ['$in' => ['message']], 'owner.nickname' => ['$in' => $authorName]])
                ->will($this->returnValue($cursor));

        $this->sut->findWallEntries($user, $wallFilter);
    }

    public function testFindWallAll()
    {
        $cursor = new CollectionIterator($this->getMock('MongoCursor', [], [], '', false), $this->repository);
        $this->repository->expects($this->once())
                ->method('find')
                ->with(['-class' => ['$in' => ['message']]])
                ->will($this->returnValue($cursor));

        $this->sut->findWallEntries($this->createNetizen('kirk'), 'all');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidFilter()
    {
        $this->sut->findWallEntries($this->createNetizen('kirk'), 'sdsdqs');
    }

}