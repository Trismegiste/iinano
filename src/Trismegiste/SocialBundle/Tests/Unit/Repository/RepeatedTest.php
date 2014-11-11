<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\PublishingRepository;
use Trismegiste\Yuurei\Persistence\CollectionIterator;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Security\Netizen;

/**
 * RepeatedTest tests the PublishingRepository only for Repeated content
 */
class RepeatedTest extends \PHPUnit_Framework_TestCase
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
        $this->sut = new PublishingRepository($this->repository, $this->security, [
            'message' => get_class($this->document),
            'repeat' => 'Trismegiste\Socialist\Repeat'
        ]);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage repeat yourself
     */
    public function testNotRepeatOneself()
    {
        $pk = '54390582e3f43405428b4568';
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo($pk))
                ->will($this->returnValue($this->document));

        $this->sut->repeatPublishing($pk);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage already
     */
    public function testNotRepeatTwoTimes()
    {
        $another = new Author('spock');
        $source = $this->getMockBuilder('Trismegiste\Socialist\Publishing')
                ->setConstructorArgs([$another])
                ->setMethods(null)
                ->getMock();

        $pk = '54390582e3f43405428b4568';
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo($pk))
                ->will($this->returnValue($source));

        $this->repository->expects($this->once())
                ->method('findOne')
                ->will($this->returnValue(true));

        $this->sut->repeatPublishing($pk);
    }

    public function testRepeatAndPersist()
    {
        $another = new Author('spock');
        $source = $this->getMockBuilder('Trismegiste\Socialist\Publishing')
                ->setConstructorArgs([$another])
                ->setMethods(null)
                ->getMock();

        $pk = '54390582e3f43405428b4568';
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo($pk))
                ->will($this->returnValue($source));

        $this->repository->expects($this->once())
                ->method('persist');

        $this->sut->repeatPublishing($pk);
    }

}
