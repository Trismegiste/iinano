<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\PrivateMessageRepository;
use Trismegiste\Socialist\Author;
use Trismegiste\Yuurei\Persistence\CollectionIterator;

/**
 * PrivateMessageRepositoryTest tests PrivateMessageRepository
 */
class PrivateMessageRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var PrivateMessageRepository */
    protected $sut;
    protected $repository;
    protected $security;
    protected $source;
    protected $target;
    protected $document;
    protected $currentUser;
    protected $token;

    protected function setUp()
    {
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->sut = new PrivateMessageRepository($this->repository, $this->security, 'alias');
        $this->source = new Author('kirk');
        $this->target = new Author('spock');
        $this->document = $this->getMockBuilder('Trismegiste\Socialist\PrivateMessage')
                ->setConstructorArgs([$this->source, $this->target])
                ->setMethods([])
                ->getMock();

        $this->currentUser = $this->getMockBuilder('Trismegiste\SocialBundle\Security\Netizen')
                ->disableOriginalConstructor()
                ->getMock();
        $this->currentUser->expects($this->any())
                ->method('getAuthor')
                ->will($this->returnValue($this->source));

        $this->token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->token->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue($this->currentUser));

        $this->security->expects($this->any())
                ->method('getToken')
                ->will($this->returnValue($this->token));
    }

    protected function createMockCursor()
    {
        return new CollectionIterator($this->getMock('MongoCursor', [], [], '', false), $this->repository);
    }

    public function testReceived()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo('ROLE_USER'))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('find')
                ->will($this->returnValue($this->createMockCursor()));

        $this->sut->findAllReceived();
    }

    public function testSent()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo('ROLE_USER'))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('find')
                ->will($this->returnValue($this->createMockCursor()));

        $this->sut->findAllSent();
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testReceivedNotLogged()
    {
        $this->sut->findAllReceived();
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testSentNotLogged()
    {
        $this->sut->findAllReceived();
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testNewMessageToNonFollower()
    {
        $this->security->expects($this->at(0))
                ->method('isGranted')
                ->with($this->equalTo('LISTENER'))
                ->will($this->returnValue(false));

        $this->sut->createNewMessageTo($this->target);
    }

    public function testNewMessageToFollower()
    {
        $this->security->expects($this->at(0))
                ->method('isGranted')
                ->with($this->equalTo('LISTENER'))
                ->will($this->returnValue(true));

        $this->security->expects($this->at(1))
                ->method('isGranted')
                ->with($this->equalTo('ROLE_USER'))
                ->will($this->returnValue(true));

        $this->assertInstanceOf('Trismegiste\Socialist\PrivateMessage', $this->sut->createNewMessageTo($this->target));
    }

    public function testPersist()
    {
        $this->repository->expects($this->once())
                ->method('persist');

        $this->sut->persist($this->document);
    }

}
