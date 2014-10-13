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

    use \Trismegiste\SocialBundle\Tests\Helper\SecurityContextMock;

    /** @var PublishingRepository */
    protected $sut;
    protected $repository;
    protected $author;
    protected $document;
    protected $security;

    protected function setUp()
    {
        $this->author = new Author('kirk');
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->security = $this->createSecurityContextMock($this->author);
        $this->security->expects($this->any())
                ->method('isGranted')
                ->will($this->returnValue(true));
        $this->document = $this->getMockBuilder('Trismegiste\Socialist\Publishing')
                ->setConstructorArgs([$this->author])
                ->setMethods(null)
                ->getMock();
        $this->sut = new PublishingRepository($this->repository, $this->security, ['message' => get_class($this->document)]);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage not found
     */
    public function testNotFoundGetByPk()
    {
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123));

        $this->sut->findByPk(123);
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage subclass
     */
    public function testInvalidTypeGetByPk()
    {
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123))
                ->will($this->returnValue($this->getMock('Trismegiste\Yuurei\Persistence\Persistable')));

        $this->sut->findByPk(123);
    }

    public function testGetByPk()
    {
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123))
                ->will($this->returnValue($this->document));

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

        $this->sut->findLastEntries(0, 20, new \ArrayIterator([$this->author->getNickname() => true]));
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

    /**
     * This because I don't want to forget new method in the interface
     */
    public function testInterfaceInSync()
    {
        $this->assertEquals(count(get_class_methods('Trismegiste\SocialBundle\Repository\PublishingRepository'))
                , count(get_class_methods('Trismegiste\SocialBundle\Repository\PublishingRepositoryInterface')) +
                count(get_class_methods('Trismegiste\SocialBundle\Repository\PublishingFactory')) +
                1
        );
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage desu
     */
    public function testInvalidCreate()
    {
        $this->sut->create('desu');
    }

    public function testCreate()
    {
        $this->sut->create('message');
    }

    public function testDeleteByPk()
    {
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo('54390582e3f43405428b4568'))
                ->will($this->returnValue($this->document));

        $this->sut->delete('54390582e3f43405428b4568', $this->getMock('MongoCollection', [], [], '', false));
    }

    public function testGetClassAlias()
    {
        $this->assertEquals('message', $this->sut->getClassAlias($this->document));
    }

    public function testPersistLikeThat()
    {
        $this->repository->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($this->document));

        $this->sut->persistLikeThat($this->document);
        $this->assertEquals(1, $this->document->getFanCount());
    }

    public function testIUnlikeThat()
    {
        $this->document->addFan($this->author);
        $this->assertEquals(1, $this->document->getFanCount());

        $this->repository->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($this->document));

        $this->sut->persistUnlikeThat($this->document);
        $this->assertEquals(0, $this->document->getFanCount());
    }

    public function testIReportThat()
    {
        $this->assertAttributeCount(0, 'abusive', $this->document);

        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo('54390582e3f43405428b4568'))
                ->will($this->returnValue($this->document));

        $this->sut->iReportThat('54390582e3f43405428b4568');
        $this->assertAttributeCount(1, 'abusive', $this->document);
    }

}
