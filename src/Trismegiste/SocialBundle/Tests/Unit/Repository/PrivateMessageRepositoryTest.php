<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\PrivateMessageRepository;
use Trismegiste\Socialist\Author;
use Trismegiste\Yuurei\Persistence\CollectionIterator;
use Trismegiste\Socialist\PrivateMessage;

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
        $this->document = new PrivateMessage($this->source, $this->target);

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
                ->with($this->equalTo('VALID_TICKET'))
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
                ->with($this->equalTo('VALID_TICKET'))
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
                ->with($this->equalTo('VALID_TICKET'))
                ->will($this->returnValue(true));

        $this->assertInstanceOf('Trismegiste\Socialist\PrivateMessage', $this->sut->createNewMessageTo($this->target));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @expectedExceptionMessage sender
     */
    public function testOnlySenderCanPersist()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo('VALID_TICKET'))
                ->will($this->returnValue(true));

        $msg = new \Trismegiste\Socialist\PrivateMessage($this->target, $this->source);

        $this->sut->persist($msg);
    }

    public function testPersist()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo('VALID_TICKET'))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('persist');

        $msg = new \Trismegiste\Socialist\PrivateMessage($this->source, $this->target);

        $this->sut->persist($msg);
    }

    /**
     * @expectedException \LogicException
     */
    public function testInvalidPkPersistAsRead()
    {
        $this->sut->persistAsRead(123);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @expectedExceptionMessage receipient
     */
    public function testOnlyReceipientCanPersistAsRead()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo('VALID_TICKET'))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123))
                ->will($this->returnValue($this->document));

        $this->sut->persistAsRead(123);
    }

    public function testPersistAsRead()
    {
        $received = new \Trismegiste\Socialist\PrivateMessage($this->target, $this->source);

        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo('VALID_TICKET'))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123))
                ->will($this->returnValue($received));

        $this->repository->expects($this->once())
                ->method('persist')
                ->with($this->attributeEqualTo('read', true));

        $this->sut->persistAsRead(123);
    }

}
