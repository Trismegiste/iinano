<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\PublishingRepository;
use Trismegiste\Yuurei\Persistence\CollectionIterator;
use Trismegiste\Socialist\Author;

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

}