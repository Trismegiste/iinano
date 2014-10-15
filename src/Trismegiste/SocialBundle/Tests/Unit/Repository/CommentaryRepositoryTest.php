<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\CommentaryRepository;
use Trismegiste\Socialist\Author;

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
                ->setMethods(null)
                ->getMock();
        $this->sut = new CommentaryRepository($this->repository, $this->security);
    }

    public function testCreate()
    {
        $comm = $this->sut->create();
        $this->assertEquals($this->author, $comm->getAuthor());
    }

}
