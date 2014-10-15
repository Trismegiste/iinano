<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\CommentaryRepository;
use Trismegiste\Socialist\Author;
use Trismegiste\Socialist\Commentary;

/**
 * CommentaryRepositoryTest tests CommentaryRepository
 */
class CommentaryRepositoryTest extends \PHPUnit_Framework_TestCase
{

    use \Trismegiste\SocialBundle\Tests\Helper\SecurityContextMock;

    /** @var CommentaryRepository */
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
                ->getMock();
        $this->sut = new CommentaryRepository($this->repository, $this->security);
    }

    public function testCreate()
    {
        $comm = $this->sut->create();
        $this->assertEquals($this->author, $comm->getAuthor());

        return $comm;
    }

    /**
     * @depends testCreate
     */
    public function testFindByUuid(Commentary $comm)
    {
        $this->document->expects($this->once())
                ->method('getCommentaryByUuid')
                ->with($this->equalTo('123'))
                ->will($this->returnValue($comm));

        $comm = $this->sut->findByUuid($this->document, '123');
    }

    /**
     * @depends testCreate
     */
    public function testPersist(Commentary $comm)
    {
        $this->repository->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($this->document));

        $this->sut->persist($this->document, $comm);
    }

    /**
     * @depends testCreate
     */
    public function testAttachAndPersist(Commentary $comm)
    {
        $this->document->expects($this->once())
                ->method('attachCommentary')
                ->with($this->equalTo($comm));

        $this->repository->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($this->document));

        $this->sut->attachAndPersist($this->document, $comm);
    }

    /**
     * @depends testCreate
     */
    public function testDetachAndPersist(Commentary $comm)
    {
        $this->document->expects($this->once())
                ->method('getCommentaryByUuid')
                ->with($this->equalTo('123'))
                ->will($this->returnValue($comm));

        $this->document->expects($this->once())
                ->method('detachCommentary')
                ->with($this->equalTo($comm));

        $this->repository->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($this->document));

        $this->sut->detachAndPersist($this->document, '123');
    }

    /**
     * @depends testCreate
     */
    public function testILikeThat(Commentary $comm)
    {
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo('54390582e3f43405428b4568'))
                ->will($this->returnValue($this->document));

        $this->document->expects($this->once())
                ->method('getCommentaryByUuid')
                ->with($this->equalTo('123'))
                ->will($this->returnValue($comm));

        $this->sut->iLikeThat('54390582e3f43405428b4568', '123');

        $this->assertEquals(1, $comm->getFanCount());

        return $comm;
    }

    /**
     * @depends testILikeThat
     */
    public function testIUnlikeThat(Commentary $comm)
    {
        $this->assertEquals(1, $comm->getFanCount());

        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo('54390582e3f43405428b4568'))
                ->will($this->returnValue($this->document));

        $this->document->expects($this->once())
                ->method('getCommentaryByUuid')
                ->with($this->equalTo('123'))
                ->will($this->returnValue($comm));

        $this->sut->iUnlikeThat('54390582e3f43405428b4568', '123');

        $this->assertEquals(0, $comm->getFanCount());
    }

    /**
     * @depends testCreate
     */
    public function testIReportThat(Commentary $comm)
    {
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo('54390582e3f43405428b4568'))
                ->will($this->returnValue($this->document));

        $this->document->expects($this->once())
                ->method('getCommentaryByUuid')
                ->with($this->equalTo('123'))
                ->will($this->returnValue($comm));

        $this->sut->iReportThat('54390582e3f43405428b4568', '123');

        $this->assertAttributeCount(1, 'abusive', $comm);
    }

}
